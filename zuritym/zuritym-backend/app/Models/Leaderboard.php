<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Leaderboard extends Model {
    protected $fillable = ['user_id','rank','total_points','weekly_points','monthly_points'];
    public function user() { return $this->belongsTo(User::class); }
}
