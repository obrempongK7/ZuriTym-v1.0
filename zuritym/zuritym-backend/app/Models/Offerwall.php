<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Offerwall extends Model {
    protected $fillable = ['name','slug','type','api_key','api_secret','url','postback_secret','icon','conversion_rate','is_active','config','sort_order'];
    protected $casts = ['is_active'=>'boolean','config'=>'array'];
    protected $hidden = ['api_key','api_secret','postback_secret'];
    public function completions() { return $this->hasMany(OfferwallCompletion::class); }
}
