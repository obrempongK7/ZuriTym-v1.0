<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FraudDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(protected FraudDetectionService $fraudService) {}

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6|confirmed',
            'device_id'   => 'required|string',
            'referral_code' => 'nullable|string|exists:users,referral_code',
            'fcm_token'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        // Anti-fraud: check device and IP limits
        $fraud = $this->fraudService->checkRegistration(
            $request->ip(),
            $request->device_id
        );
        if ($fraud['blocked']) {
            return $this->error($fraud['reason'], 403);
        }

        $referredBy = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            $referredBy = $referrer?->id;
        }

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => $request->password,
            'device_id'         => $request->device_id,
            'device_fingerprint' => $request->device_fingerprint,
            'registration_ip'   => $request->ip(),
            'last_ip'           => $request->ip(),
            'referred_by'       => $referredBy,
            'fcm_token'         => $request->fcm_token,
            'country'           => $request->country,
        ]);

        // Award referral bonus
        if ($referredBy) {
            $referrer = User::find($referredBy);
            $bonus = (float) config('zuritym.referral_reward', 50);
            $referrer->creditWallet($bonus, 'referral', "Referral bonus: {$user->name} joined");
            $referrer->increment('total_referrals');
            // New user signup bonus
            $user->creditWallet(
                (float) config('zuritym.signup_bonus', 10),
                'bonus', 'Welcome bonus for joining ZuriTym!'
            );
        }

        $token = $user->createToken('zuritym-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $this->userResource($user->fresh()),
        ], 'Registration successful! Welcome to ZuriTym.');
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string',
            'device_id' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        if ($user->isBlocked()) {
            return $this->error('Your account has been suspended. Reason: ' . $user->block_reason, 403);
        }

        // Update device & IP info
        $updateData = ['last_login_at' => now(), 'last_ip' => $request->ip()];
        if ($request->device_id) $updateData['device_id'] = $request->device_id;
        if ($request->fcm_token) $updateData['fcm_token'] = $request->fcm_token;
        $user->update($updateData);

        // Device fraud check (soft - don't block on login, just log)
        $this->fraudService->checkDeviceOnLogin($user, $request->device_id);

        $user->tokens()->delete(); // Single session
        $token = $user->createToken('zuritym-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $this->userResource($user->fresh()),
        ], 'Login successful!');
    }

    public function googleAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token'  => 'required|string',
            'device_id' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        try {
            $googleUser = Socialite::driver('google')->userFromToken($request->id_token);
        } catch (\Exception $e) {
            return $this->error('Invalid Google token.', 401);
        }

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        $isNew = false;
        if (!$user) {
            $fraud = $this->fraudService->checkRegistration($request->ip(), $request->device_id);
            if ($fraud['blocked']) {
                return $this->error($fraud['reason'], 403);
            }
            $user = User::create([
                'name'            => $googleUser->getName(),
                'email'           => $googleUser->getEmail(),
                'google_id'       => $googleUser->getId(),
                'avatar'          => $googleUser->getAvatar(),
                'is_verified'     => true,
                'device_id'       => $request->device_id,
                'registration_ip' => $request->ip(),
                'last_ip'         => $request->ip(),
                'fcm_token'       => $request->fcm_token,
            ]);
            $user->creditWallet(
                (float) config('zuritym.signup_bonus', 10),
                'bonus', 'Welcome bonus for joining ZuriTym!'
            );
            $isNew = true;
        } else {
            if ($user->isBlocked()) {
                return $this->error('Account suspended. ' . $user->block_reason, 403);
            }
            $user->update([
                'google_id'     => $googleUser->getId(),
                'last_login_at' => now(),
                'last_ip'       => $request->ip(),
                'fcm_token'     => $request->fcm_token ?? $user->fcm_token,
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('zuritym-app')->plainTextToken;

        return $this->success([
            'token'  => $token,
            'user'   => $this->userResource($user->fresh()),
            'is_new' => $isNew,
        ], $isNew ? 'Welcome to ZuriTym!' : 'Login successful!');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('wallet', 'leaderboard');
        return $this->success($this->userResource($user));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:100',
            'username' => 'sometimes|string|max:30|unique:users,username,' . $user->id,
            'phone'    => 'sometimes|nullable|string|max:20',
            'avatar'   => 'sometimes|nullable|image|max:2048',
            'fcm_token' => 'sometimes|nullable|string',
        ]);
        if ($validator->fails()) return $this->error($validator->errors()->first(), 422);

        $data = $request->only(['name', 'username', 'phone', 'fcm_token']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = basename($path);
        }

        $user->update($data);
        return $this->success($this->userResource($user->fresh()), 'Profile updated.');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) return $this->error($validator->errors()->first(), 422);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect.', 422);
        }
        $user->update(['password' => $request->new_password]);
        return $this->success([], 'Password changed successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success([], 'Logged out successfully.');
    }

    private function userResource(User $user): array
    {
        return [
            'id'              => $user->id,
            'name'            => $user->name,
            'username'        => $user->username,
            'email'           => $user->email,
            'phone'           => $user->phone,
            'avatar_url'      => $user->avatar_url,
            'referral_code'   => $user->referral_code,
            'total_referrals' => $user->total_referrals,
            'is_verified'     => $user->is_verified,
            'country'         => $user->country,
            'wallet'          => [
                'balance'         => $user->wallet?->balance ?? 0,
                'total_earned'    => $user->wallet?->total_earned ?? 0,
                'total_withdrawn' => $user->wallet?->total_withdrawn ?? 0,
            ],
            'rank'            => $user->leaderboard?->rank ?? 0,
            'joined_at'       => $user->created_at?->toISOString(),
        ];
    }

    private function success($data, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    private function error(string $message, int $code = 400): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'data' => null], $code);
    }
}
