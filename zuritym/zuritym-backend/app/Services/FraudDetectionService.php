<?php

namespace App\Services;

use App\Models\FraudLog;
use App\Models\User;

class FraudDetectionService
{
    public function checkRegistration(string $ip, ?string $deviceId): array
    {
        $maxPerIp     = (int) config('zuritym.fraud_max_accounts_per_ip', 2);
        $maxPerDevice = (int) config('zuritym.fraud_max_accounts_per_device', 1);

        // Check IP limit
        $ipCount = User::where('registration_ip', $ip)->count();
        if ($ipCount >= $maxPerIp) {
            FraudLog::create([
                'event_type'  => 'registration_ip_limit',
                'ip_address'  => $ip,
                'device_id'   => $deviceId,
                'description' => "Registration blocked: IP limit reached ({$ipCount}/{$maxPerIp})",
                'severity'    => 'high',
            ]);
            return ['blocked' => true, 'reason' => 'Registration not allowed from this network.'];
        }

        // Check device limit
        if ($deviceId) {
            $deviceCount = User::where('device_id', $deviceId)->count();
            if ($deviceCount >= $maxPerDevice) {
                FraudLog::create([
                    'event_type'  => 'registration_device_limit',
                    'ip_address'  => $ip,
                    'device_id'   => $deviceId,
                    'description' => "Registration blocked: device limit ({$deviceCount}/{$maxPerDevice})",
                    'severity'    => 'critical',
                ]);
                return ['blocked' => true, 'reason' => 'This device already has an account.'];
            }
        }

        return ['blocked' => false, 'reason' => null];
    }

    public function checkDeviceOnLogin(User $user, ?string $deviceId): void
    {
        if (!$deviceId || !$user->device_id) return;
        if ($user->device_id !== $deviceId) {
            FraudLog::create([
                'user_id'     => $user->id,
                'event_type'  => 'device_mismatch',
                'ip_address'  => request()->ip(),
                'device_id'   => $deviceId,
                'description' => "Device mismatch on login. Registered: {$user->device_id}, Current: {$deviceId}",
                'severity'    => 'medium',
            ]);
            $user->incrementFraudScore(15, 'Login from different device');
        }
    }

    public function detectTaskFraud(User $user, int $taskId): bool
    {
        // Rapid task completion check
        $recentTaskCount = $user->userTasks()
                                ->where('task_id', $taskId)
                                ->where('created_at', '>=', now()->subMinutes(5))
                                ->count();
        if ($recentTaskCount >= 3) {
            $user->incrementFraudScore(25, 'Rapid task completion attempts');
            FraudLog::create([
                'user_id'     => $user->id,
                'event_type'  => 'rapid_task_completion',
                'ip_address'  => request()->ip(),
                'description' => "Rapid task completion: {$recentTaskCount} in 5 mins",
                'severity'    => 'high',
            ]);
            return true;
        }
        return false;
    }

    public function detectVpnOrProxy(string $ip): bool
    {
        // Basic check - can integrate with ipqualityscore or similar
        $suspiciousRanges = ['10.', '172.16.', '192.168.']; // private IPs as example
        foreach ($suspiciousRanges as $range) {
            if (str_starts_with($ip, $range)) return false; // local dev
        }
        return false; // Extend with actual VPN detection API
    }
}
