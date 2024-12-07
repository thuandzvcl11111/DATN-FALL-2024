<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBlog extends Model
{
    use HasFactory;

    protected $table = 'categories_blog';

    protected $fillable = ['name'];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
