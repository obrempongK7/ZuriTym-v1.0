<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Offerwall;
use App\Models\OfferwallCompletion;
use App\Models\ScratchCard;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// ─────────────────────────────────────────────────────────────────
class ScratchCardController extends Controller
{
    private array $prizes = [
        ['label' => '5 pts',   'points' => 5,    'probability' => 35],
        ['label' => '10 pts',  'points' => 10,   'probability' => 25],
        ['label' => '20 pts',  'points' => 20,   'probability' => 18],
        ['label' => '50 pts',  'points' => 50,   'probability' => 12],
        ['label' => '100 pts', 'points' => 100,  'probability' => 7],
        ['label' => '200 pts', 'points' => 200,  'probability' => 2],
        ['label' => '500 pts', 'points' => 500,  'probability' => 1],
    ];

    public function issue(Request $request): JsonResponse
    {
        $user       = $request->user();
        $dailyLimit = (int) Setting::get('scratch_daily_limit', 5);
        $todayCount = ScratchCard::where('user_id', $user->id)
                                 ->whereDate('created_at', today())
                                 ->count();

        if ($todayCount >= $dailyLimit) {
            return $this->error("Daily scratch card limit reached ({$dailyLimit}/day). Come back tomorrow!", 429);
        }

        $points = $this->selectPrize();
        $card   = ScratchCard::create(['user_id' => $user->id, 'points_won' => $points]);

        return $this->success([
            'card_id'         => $card->id,
            'cards_remaining' => $dailyLimit - $todayCount - 1,
        ], 'New scratch card issued! Scratch to reveal your prize.');
    }

    public function scratch(Request $request, int $cardId): JsonResponse
    {
        $user = $request->user();
        $card = ScratchCard::where('id', $cardId)
                           ->where('user_id', $user->id)
                           ->where('is_scratched', false)
                           ->firstOrFail();

        DB::transaction(function () use ($user, $card) {
            $card->update(['is_scratched' => true, 'scratched_at' => now()]);
            if ($card->points_won > 0) {
                $user->creditWallet($card->points_won, 'scratch', "Scratch card reward: {$card->points_won} pts");
            }
        });

        return $this->success([
            'points_won'  => $card->points_won,
            'new_balance' => $user->fresh()->wallet->balance,
        ], $card->points_won > 0 ? "🎊 You won {$card->points_won} points!" : "Try again next time!");
    }

    private function selectPrize(): int
    {
        $total = array_sum(array_column($this->prizes, 'probability'));
        $rand  = mt_rand(1, $total);
        $cum   = 0;
        foreach ($this->prizes as $prize) {
            $cum += $prize['probability'];
            if ($rand <= $cum) return $prize['points'];
        }
        return $this->prizes[0]['points'];
    }

    private function success($data, string $msg = 'OK', int $code = 200): JsonResponse
    { return response()->json(['success'=>true,'message'=>$msg,'data'=>$data],$code); }
    private function error(string $msg, int $code = 400): JsonResponse
    { return response()->json(['success'=>false,'message'=>$msg,'data'=>null],$code); }
}

// ─────────────────────────────────────────────────────────────────
class OfferwallController extends Controller
{
    public function index(): JsonResponse
    {
        $walls = Offerwall::where('is_active', true)
                          ->orderBy('sort_order')
                          ->get()
                          ->map(fn($w) => [
                              'id'              => $w->id,
                              'name'            => $w->name,
                              'slug'            => $w->slug,
                              'type'            => $w->type,
                              'icon_url'        => $w->icon ? asset('storage/offerwalls/' . $w->icon) : null,
                              'conversion_rate' => $w->conversion_rate,
                              'url'             => $w->type === 'web' ? $w->url : null,
                          ]);

        return $this->success($walls);
    }

    public function getUrl(Request $request, Offerwall $offerwall): JsonResponse
    {
        $user = $request->user();
        if (!$offerwall->is_active) return $this->error('Offerwall not available.', 404);

        // Build personalized offerwall URL with user params
        $url = $offerwall->url;
        if ($url) {
            $url = str_replace(
                ['{user_id}', '{email}', '{name}'],
                [$user->id, $user->email, urlencode($user->name)],
                $url
            );
        }

        return $this->success(['url' => $url, 'type' => $offerwall->type]);
    }

    public function postback(Request $request, string $slug): JsonResponse
    {
        $offerwall = Offerwall::where('slug', $slug)->firstOrFail();

        // Verify postback secret
        $secret = $request->header('X-Postback-Secret') ?? $request->secret;
        if ($offerwall->postback_secret && !Hash::check($secret, $offerwall->postback_secret)) {
            return response()->json(['error' => 'Invalid secret'], 403);
        }

        $userId  = $request->user_id ?? $request->uid;
        $payout  = (float) ($request->payout ?? $request->reward ?? 0);
        $offerId = $request->offer_id ?? $request->oid;
        $txnId   = $request->transaction_id ?? $request->tid ?? uniqid();

        $existing = OfferwallCompletion::where('transaction_id', $txnId)->first();
        if ($existing) return response()->json(['status' => 'duplicate'], 200);

        $user = \App\Models\User::find($userId);
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $pointsAwarded = $payout * $offerwall->conversion_rate;

        DB::transaction(function () use ($user, $offerwall, $offerId, $payout, $pointsAwarded, $txnId, $request) {
            OfferwallCompletion::create([
                'user_id'        => $user->id,
                'offerwall_id'   => $offerwall->id,
                'offer_id'       => $offerId,
                'offer_name'     => $request->offer_name,
                'payout'         => $payout,
                'points_awarded' => $pointsAwarded,
                'status'         => 'completed',
                'transaction_id' => $txnId,
                'postback_data'  => $request->all(),
            ]);
            $user->creditWallet($pointsAwarded, 'offerwall', "Offerwall: {$offerwall->name} - {$request->offer_name}");
        });

        return response()->json(['status' => 'ok'], 200);
    }

    private function success($data, string $msg = 'OK', int $code = 200): JsonResponse
    { return response()->json(['success'=>true,'message'=>$msg,'data'=>$data],$code); }
    private function error(string $msg, int $code = 400): JsonResponse
    { return response()->json(['success'=>false,'message'=>$msg,'data'=>null],$code); }
}

// ─────────────────────────────────────────────────────────────────
class LeaderboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $period = $request->get('period', 'all'); // all, weekly, monthly
        $column = match($period) {
            'weekly'  => 'weekly_points',
            'monthly' => 'monthly_points',
            default   => 'total_points',
        };

        $leaders = Leaderboard::with(['user:id,name,username,avatar'])
                              ->orderByDesc($column)
                              ->take(100)
                              ->get()
                              ->map(fn($l, $i) => [
                                  'rank'         => $i + 1,
                                  'user_id'      => $l->user_id,
                                  'name'         => $l->user?->name,
                                  'username'     => $l->user?->username,
                                  'avatar_url'   => $l->user?->avatar_url,
                                  'total_points' => $l->$column,
                              ]);

        $myRank = Leaderboard::where('user_id', $request->user()->id)->value('rank');

        return response()->json([
            'success' => true,
            'data'    => [
                'leaders' => $leaders,
                'my_rank' => $myRank,
                'period'  => $period,
            ],
        ]);
    }
}
