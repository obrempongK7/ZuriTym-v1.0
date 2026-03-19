<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'phone', 'avatar',
        'google_id', 'referral_code', 'referred_by',
        'device_id', 'device_fingerprint', 'last_ip', 'registration_ip',
        'is_blocked', 'block_reason', 'blocked_at', 'fraud_score',
        'is_verified', 'status', 'role', 'last_login_at', 'fcm_token',
        'country', 'timezone', 'total_referrals',
    ];

    protected $hidden = [
        'password', 'remember_token', 'device_id', 'device_fingerprint',
        'registration_ip', 'fraud_score',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'blocked_at'        => 'datetime',
        'last_login_at'     => 'datetime',
        'is_blocked'        => 'boolean',
        'is_verified'       => 'boolean',
        'password'          => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (!$user->referral_code) {
                $user->referral_code = strtoupper(substr(md5(uniqid()), 0, 8));
            }
        });
        static::created(function ($user) {
            Wallet::create(['user_id' => $user->id]);
            Leaderboard::create(['user_id' => $user->id]);
        });
    }

    // ─── Relationships ───────────────────────────────────────────
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    public function userTasks()
    {
        return $this->hasMany(UserTask::class);
    }

    public function spinHistories()
    {
        return $this->hasMany(SpinHistory::class);
    }

    public function scratchCards()
    {
        return $this->hasMany(ScratchCard::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->latest();
    }

    public function offerwallCompletions()
    {
        return $this->hasMany(OfferwallCompletion::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function usedPromoCodes()
    {
        return $this->belongsToMany(PromoCode::class, 'user_promo_codes')
                    ->withPivot('points_earned')
                    ->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function fraudLogs()
    {
        return $this->hasMany(FraudLog::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked || $this->status === 'blocked';
    }

    public function getBalance(): float
    {
        return $this->wallet ? $this->wallet->balance : 0.0;
    }

    public function creditWallet(float $amount, string $type, string $description, array $meta = []): Transaction
    {
        $wallet = $this->wallet;
        $before = $wallet->balance;
        $wallet->increment('balance', $amount);
        $wallet->increment('total_earned', $amount);

        return Transaction::create([
            'txn_id'         => 'TXN' . strtoupper(uniqid()),
            'user_id'        => $this->id,
            'amount'         => $amount,
            'balance_before' => $before,
            'balance_after'  => $before + $amount,
            'type'           => $type,
            'status'         => 'completed',
            'description'    => $description,
            'meta'           => $meta,
            'ip_address'     => request()->ip(),
        ]);
    }

    public function debitWallet(float $amount, string $type, string $description, array $meta = []): ?Transaction
    {
        $wallet = $this->wallet;
        if ($wallet->balance < $amount) {
            return null;
        }
        $before = $wallet->balance;
        $wallet->decrement('balance', $amount);

        return Transaction::create([
            'txn_id'         => 'TXN' . strtoupper(uniqid()),
            'user_id'        => $this->id,
            'amount'         => -$amount,
            'balance_before' => $before,
            'balance_after'  => $before - $amount,
            'type'           => $type,
            'status'         => 'completed',
            'description'    => $description,
            'meta'           => $meta,
            'ip_address'     => request()->ip(),
        ]);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }
        return asset('storage/avatars/' . ($this->avatar ?? 'default_avatar.png'));
    }

    public function getTodaySpinCountAttribute(): int
    {
        return $this->spinHistories()
                    ->whereDate('created_at', today())
                    ->count();
    }

    public function getTodayScratchCountAttribute(): int
    {
        return $this->scratchCards()
                    ->whereDate('created_at', today())
                    ->count();
    }

    public function incrementFraudScore(int $points, string $reason): void
    {
        $this->increment('fraud_score', $points);
        FraudLog::create([
            'user_id'     => $this->id,
            'event_type'  => 'score_increment',
            'ip_address'  => request()->ip(),
            'description' => $reason,
            'severity'    => $points >= 30 ? 'high' : ($points >= 15 ? 'medium' : 'low'),
        ]);
        if ($this->fraud_score >= 100) {
            $this->blockUser('Auto-blocked: fraud score exceeded threshold.');
        }
    }

    public function blockUser(string $reason): void
    {
        $this->update([
            'is_blocked'   => true,
            'status'       => 'blocked',
            'block_reason' => $reason,
            'blocked_at'   => now(),
        ]);
        if ($this->wallet) {
            $this->wallet->update(['is_locked' => true, 'lock_reason' => $reason]);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_blocked', false);
    }
}
