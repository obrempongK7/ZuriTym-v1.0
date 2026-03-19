<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentMethod extends Model {
    protected $fillable = ['name','slug','icon','min_withdrawal','max_withdrawal','conversion_rate','fields','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean','fields'=>'array'];
}
