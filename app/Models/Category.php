<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'mota', 'parent_id'];

    // Relationship with Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relationship with child categories
    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive relationship to get all descendant categories
    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
