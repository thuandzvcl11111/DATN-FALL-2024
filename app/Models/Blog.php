<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs'; 

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'status',
        'image_path',
        'published_date',
        'is_hot',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBlog::class, 'category_id');
    }
}