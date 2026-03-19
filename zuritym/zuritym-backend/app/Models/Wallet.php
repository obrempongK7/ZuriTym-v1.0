<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
    protected $fillable = ['user_id','balance','total_earned','total_withdrawn','pending_withdrawal','bonus_balance','currency','is_locked','lock_reason'];
    protected $casts = ['is_locked' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
