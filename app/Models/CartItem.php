<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items'; // Đảm bảo tên bảng trùng khớp với cơ sở dữ liệu của bạn
    protected $fillable = [
        'user_id',
        'product_meta_id',
        'quantity',
        'price'
    ];

    // Liên kết với ProductMeta
    public function productMeta()
    {
        return $this->belongsTo(ProductMeta::class, 'product_meta_id');
    }

    // Liên kết với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
