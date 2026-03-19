<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SpinReward extends Model {
    protected $fillable = ['label','points','type','probability','color','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean'];
}
