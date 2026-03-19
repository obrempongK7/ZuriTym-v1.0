<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatMessage extends Model {
    protected $fillable = ['user_id','message','is_deleted','is_flagged'];
    protected $casts = ['is_deleted'=>'boolean','is_flagged'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
