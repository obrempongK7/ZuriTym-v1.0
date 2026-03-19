<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SpinHistory extends Model {
    protected $fillable = ['user_id','spin_reward_id','points_won'];
    public function user() { return $this->belongsTo(User::class); }
    public function spinReward() { return $this->belongsTo(SpinReward::class); }
}
