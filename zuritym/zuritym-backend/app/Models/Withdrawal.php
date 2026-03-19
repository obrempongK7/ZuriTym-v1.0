<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Withdrawal extends Model {
    protected $fillable = ['withdrawal_id','user_id','amount_points','amount_cash','payment_method','payment_details','status','screenshot','admin_note','rejection_reason','processed_by','processed_at'];
    protected $casts = ['payment_details'=>'array','processed_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function processor() { return $this->belongsTo(User::class,'processed_by'); }
}
