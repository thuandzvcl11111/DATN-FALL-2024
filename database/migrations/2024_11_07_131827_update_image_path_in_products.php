<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateImagePathInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cho phép image_path nhận giá trị NULL
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Khôi phục lại image_path không nhận NULL
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
        });
    }
}
