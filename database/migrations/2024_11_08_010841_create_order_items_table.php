<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_meta_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
    
            // Tạo khóa ngoại với bảng 'product_metas'
            $table->foreign('product_meta_id')->references('id')->on('product_meta')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('order_items');
    }

};
