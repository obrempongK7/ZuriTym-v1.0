<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScratchCard extends Model {
    protected $fillable = ['user_id','points_won','is_scratched','scratched_at'];
    protected $casts = ['is_scratched'=>'boolean','scratched_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
