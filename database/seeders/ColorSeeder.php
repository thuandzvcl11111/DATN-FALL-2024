<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run()
    {
        Color::create(['name' => 'Đỏ']);
        Color::create(['name' => 'Xanh']);
        Color::create(['name' => 'Vàng']);
        Color::create(['name' => 'Đen']);
    }
}

