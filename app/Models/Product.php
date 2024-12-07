<?php

namespace App\Models;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory; //SoftDeletes

    protected $fillable = ['name', 'description', 'price', 'category_id', 'is_hot', 'is_new', 'status', 'image_path'];


    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with ProductMeta
    public function productMeta()
    {
        return $this->hasMany(ProductMeta::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
