<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // Mật khẩu của admin
            'role' => 'admin',  // Role admin
            'phone' => '1234567890',
            'gmail' => 'admin@example.com',
            'address' => 'Admin Address',
        ]);

        // Tạo tài khoản customer
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'), // Mật khẩu của customer
            'role' => 'customer',  // Role customer
            'phone' => '0987654321',
            'gmail' => 'customer@example.com',
            'address' => 'Customer Address',
        ]);
    }
}
