<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PromoCode extends Model {
    protected $fillable = ['code','description','reward_points','type','usage_limit','usage_count','per_user_limit','expires_at','is_active'];
    protected $casts = ['is_active'=>'boolean','expires_at'=>'datetime'];
    public function isValid(): bool {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        return true;
    }
    public function users() { return $this->belongsToMany(User::class,'user_promo_codes')->withPivot('points_earned')->withTimestamps(); }
}
