<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model {
    use SoftDeletes;
    protected $fillable = [
        'title','description','icon','banner','type','reward_points','action_url',
        'timer_seconds','completion_limit','daily_limit','total_limit','completion_count',
        'is_active','requires_screenshot','is_verified','geo_target','requirements','sort_order'
    ];
    protected $casts = ['is_active'=>'boolean','requires_screenshot'=>'boolean','geo_target'=>'array','requirements'=>'array'];
    public function userTasks() { return $this->hasMany(UserTask::class); }
    public function completionsByUser(int $userId) {
        return $this->userTasks()->where('user_id', $userId)->where('status','completed')->count();
    }
}
