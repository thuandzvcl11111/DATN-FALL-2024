<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relationship with ProductMeta
    public function productMeta()
    {
        return $this->hasMany(ProductMeta::class);
    }
}
