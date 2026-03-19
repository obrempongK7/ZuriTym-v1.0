<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PromoCode;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function balance(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        return $this->success([
            'balance'           => $wallet->balance,
            'total_earned'      => $wallet->total_earned,
            'total_withdrawn'   => $wallet->total_withdrawn,
            'pending_withdrawal' => $wallet->pending_withdrawal,
            'bonus_balance'     => $wallet->bonus_balance,
            'is_locked'         => $wallet->is_locked,
            'lock_reason'       => $wallet->is_locked ? $wallet->lock_reason : null,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $txns = $request->user()
                        ->transactions()
                        ->when($request->type, fn($q) => $q->where('type', $request->type))
                        ->when($request->status, fn($q) => $q->where('status', $request->status))
                        ->paginate(20);

        return $this->success([
            'transactions' => $txns->map(fn($t) => [
                'id'             => $t->id,
                'txn_id'         => $t->txn_id,
                'amount'         => $t->amount,
                'formatted'      => $t->formatted_amount,
                'type'           => $t->type,
                'status'         => $t->status,
                'description'    => $t->description,
                'created_at'     => $t->created_at->toISOString(),
            ]),
            'pagination' => [
                'current_page' => $txns->currentPage(),
                'last_page'    => $txns->lastPage(),
                'total'        => $txns->total(),
            ],
        ]);
    }

    public function redeemPromoCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:30',
        ]);
        if ($validator->fails()) return $this->error($validator->errors()->first(), 422);

        $user  = $request->user();
        $promo = PromoCode::where('code', strtoupper($request->code))->first();

        if (!$promo || !$promo->isValid()) {
            return $this->error('Invalid or expired promo code.', 404);
        }

        $usedCount = $user->usedPromoCodes()->where('promo_code_id', $promo->id)->count();
        if ($usedCount >= $promo->per_user_limit) {
            return $this->error('You have already used this promo code.', 409);
        }

        DB::transaction(function () use ($user, $promo) {
            $points = $promo->type === 'percentage'
                ? $user->wallet->balance * ($promo->reward_points / 100)
                : $promo->reward_points;

            $user->creditWallet($points, 'promo_code', "Promo code: {$promo->code}");
            $user->usedPromoCodes()->attach($promo->id, ['points_earned' => $points]);
            $promo->increment('usage_count');
        });

        return $this->success([
            'points_earned' => $promo->reward_points,
            'new_balance'   => $user->fresh()->wallet->balance,
        ], "Promo code applied! You earned {$promo->reward_points} points.");
    }

    public function requestWithdrawal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount_points'   => 'required|numeric|min:1',
            'payment_method'  => 'required|string|exists:payment_methods,slug',
            'payment_details' => 'required|array',
            'screenshot'      => 'nullable|image|max:5120',
        ]);
        if ($validator->fails()) return $this->error($validator->errors()->first(), 422);

        $user   = $request->user();
        $wallet = $user->wallet;

        if ($wallet->is_locked) {
            return $this->error('Your wallet is locked. ' . $wallet->lock_reason, 403);
        }

        $method = PaymentMethod::where('slug', $request->payment_method)
                               ->where('is_active', true)->first();
        if (!$method) return $this->error('Payment method not available.', 404);

        if ($request->amount_points < $method->min_withdrawal) {
            return $this->error("Minimum withdrawal is {$method->min_withdrawal} points.", 422);
        }
        if ($wallet->balance < $request->amount_points) {
            return $this->error('Insufficient balance.', 422);
        }

        DB::transaction(function () use ($user, $wallet, $method, $request) {
            $cashAmount = $request->amount_points * $method->conversion_rate;

            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                $screenshotPath = $request->file('screenshot')->store('withdrawals', 'public');
            }

            $withdrawal = Withdrawal::create([
                'withdrawal_id'   => 'WD' . strtoupper(uniqid()),
                'user_id'         => $user->id,
                'amount_points'   => $request->amount_points,
                'amount_cash'     => $cashAmount,
                'payment_method'  => $request->payment_method,
                'payment_details' => $request->payment_details,
                'screenshot'      => $screenshotPath ? basename($screenshotPath) : null,
            ]);

            // Deduct from wallet & move to pending
            $wallet->decrement('balance', $request->amount_points);
            $wallet->increment('pending_withdrawal', $request->amount_points);

            $user->transactions()->create([
                'txn_id'      => 'TXN' . strtoupper(uniqid()),
                'amount'      => -$request->amount_points,
                'type'        => 'withdrawal',
                'status'      => 'pending',
                'description' => "Withdrawal request via {$request->payment_method}",
                'reference_id' => $withdrawal->id,
            ]);
        });

        return $this->success([], 'Withdrawal request submitted. It will be processed within 24-48 hours.');
    }

    public function withdrawalHistory(Request $request): JsonResponse
    {
        $withdrawals = $request->user()
                               ->withdrawals()
                               ->paginate(15);

        return $this->success([
            'withdrawals' => $withdrawals->map(fn($w) => [
                'id'             => $w->id,
                'withdrawal_id'  => $w->withdrawal_id,
                'amount_points'  => $w->amount_points,
                'amount_cash'    => $w->amount_cash,
                'payment_method' => $w->payment_method,
                'status'         => $w->status,
                'admin_note'     => $w->admin_note,
                'created_at'     => $w->created_at->toISOString(),
                'processed_at'   => $w->processed_at?->toISOString(),
            ]),
            'pagination' => [
                'current_page' => $withdrawals->currentPage(),
                'last_page'    => $withdrawals->lastPage(),
                'total'        => $withdrawals->total(),
            ],
        ]);
    }

    public function paymentMethods(): JsonResponse
    {
        $methods = PaymentMethod::where('is_active', true)
                                ->orderBy('sort_order')
                                ->get()
                                ->map(fn($m) => [
                                    'id'              => $m->id,
                                    'name'            => $m->name,
                                    'slug'            => $m->slug,
                                    'icon_url'        => $m->icon ? asset('storage/icons/' . $m->icon) : null,
                                    'min_withdrawal'  => $m->min_withdrawal,
                                    'max_withdrawal'  => $m->max_withdrawal,
                                    'conversion_rate' => $m->conversion_rate,
                                    'fields'          => $m->fields,
                                ]);

        return $this->success($methods);
    }

    private function success($data, string $msg = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data], $code);
    }
    private function error(string $msg, int $code = 400): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $msg, 'data' => null], $code);
    }
}
