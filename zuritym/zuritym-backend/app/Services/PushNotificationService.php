<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): bool
    {
        if (empty($tokens)) return false;

        $serverKey = config('services.firebase.server_key');
        if (!$serverKey) {
            Log::warning('FCM server key not configured.');
            return false;
        }

        // Send in batches of 500 (FCM limit)
        $chunks = array_chunk($tokens, 500);
        foreach ($chunks as $chunk) {
            try {
                Http::withHeaders([
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type'  => 'application/json',
                ])->post($this->fcmUrl, [
                    'registration_ids' => $chunk,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'sound' => 'default',
                        'icon'  => 'notification_icon',
                    ],
                    'data' => $data,
                    'priority' => 'high',
                ]);
            } catch (\Exception $e) {
                Log::error('FCM push failed: ' . $e->getMessage());
            }
        }
        return true;
    }

    public function sendToUser(\App\Models\User $user, string $title, string $body, array $data = []): bool
    {
        if (!$user->fcm_token) return false;
        return $this->sendToTokens([$user->fcm_token], $title, $body, $data);
    }
}
