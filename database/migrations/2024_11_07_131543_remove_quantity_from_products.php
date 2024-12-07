<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveQuantityFromProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xóa cột 'quantity' trong bảng 'products'
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Thêm lại cột 'quantity' trong bảng 'products' nếu cần rollback
        Schema::table('products', function (Blueprint $table) {
            $table->integer('quantity')->default(0);
        });
    }
}
