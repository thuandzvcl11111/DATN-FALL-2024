<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'shipping_address',
        'phone_number',
        'status',
        'name_coupon',
        'sub_total',
        'sale_price'

    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
