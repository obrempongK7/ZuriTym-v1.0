<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdNetwork extends Model {
    protected $fillable = ['name','slug','config','is_active','sort_order'];
    protected $casts = ['config'=>'array','is_active'=>'boolean'];
}
