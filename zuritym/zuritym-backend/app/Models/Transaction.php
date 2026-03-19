<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'txn_id', 'user_id', 'amount', 'balance_before', 'balance_after',
        'type', 'status', 'description', 'reference_id', 'reference_type',
        'meta', 'ip_address',
    ];
    protected $casts = ['meta' => 'array'];

    public function user() { return $this->belongsTo(User::class); }

    public function getIsDebitAttribute(): bool { return $this->amount < 0; }
    public function getIsCreditAttribute(): bool { return $this->amount > 0; }
    public function getFormattedAmountAttribute(): string {
        $sign = $this->amount >= 0 ? '+' : '';
        return $sign . number_format($this->amount, 0) . ' pts';
    }
}
