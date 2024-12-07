<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    use HasFactory;
    protected $table = 'bannerslide';
    protected $fillable = ['image','status'];
}
