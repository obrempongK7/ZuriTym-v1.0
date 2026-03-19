<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FraudLog extends Model {
    protected $fillable = ['user_id','event_type','ip_address','device_id','description','meta','severity','is_reviewed'];
    protected $casts = ['meta'=>'array','is_reviewed'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
