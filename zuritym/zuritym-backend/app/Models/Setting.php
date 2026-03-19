<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
    protected $fillable = ['key','value','group','type','description'];
    public static function get(string $key, $default = null) {
        $s = static::where('key',$key)->first();
        return $s ? $s->value : $default;
    }
    public static function set(string $key, $value, string $group = 'general'): void {
        static::updateOrCreate(['key'=>$key],['value'=>$value,'group'=>$group]);
    }
}
