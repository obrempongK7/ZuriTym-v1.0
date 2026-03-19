<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OfferwallCompletion extends Model {
    protected $fillable = ['user_id','offerwall_id','offer_id','offer_name','payout','points_awarded','status','transaction_id','postback_data'];
    protected $casts = ['postback_data'=>'array'];
    public function user() { return $this->belongsTo(User::class); }
    public function offerwall() { return $this->belongsTo(Offerwall::class); }
}
