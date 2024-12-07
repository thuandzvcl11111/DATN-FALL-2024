<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Category::create(['name' => 'Áo']);
        // Category::create(['name' => 'Quần']);
        // Category::create(['name' => 'Giày']);
        // Category::create(['name' => 'Phụ kiện']);

        $electronics = Category::create([
            'name' => 'Áo',
            'mota' => 'Các sản phẩm điện tử',
            'parent_id' => null
        ]);

        $fashion = Category::create([
            'name' => 'Quần',
            'mota' => 'Các sản phẩm thời trang',
            'parent_id' => null
        ]);

        // Tạo các danh mục con cho Điện tử
        $phones = Category::create([
            'name' => 'Điện thoại',
            'mota' => 'Các loại điện thoại',
            'parent_id' => $electronics->id
        ]);

        $computers = Category::create([
            'name' => 'Máy tính',
            'mota' => 'Các loại máy tính',
            'parent_id' => $electronics->id
        ]);

        // Tạo các danh mục con cho Điện thoại
        Category::create([
            'name' => 'Smartphone',
            'mota' => 'Các loại điện thoại thông minh',
            'parent_id' => $phones->id
        ]);

        Category::create([
            'name' => 'Feature Phone',
            'mota' => 'Các loại điện thoại phổ thông',
            'parent_id' => $phones->id
        ]);

        // Tạo một số danh mục con cho Thời trang
        Category::create([
            'name' => 'Nam',
            'mota' => 'Thời trang nam',
            'parent_id' => $fashion->id
        ]);

        Category::create([
            'name' => 'Nữ',
            'mota' => 'Thời trang nữ',
            'parent_id' => $fashion->id
        ]);
    }
}
