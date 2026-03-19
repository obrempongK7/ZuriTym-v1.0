<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserTask extends Model {
    protected $fillable = ['user_id','task_id','status','earned_points','screenshot','proof_url','rejection_reason','ip_address','device_id'];
    public function user() { return $this->belongsTo(User::class); }
    public function task() { return $this->belongsTo(Task::class); }
}
