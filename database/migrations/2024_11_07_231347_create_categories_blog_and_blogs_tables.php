<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesBlogAndBlogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tạo bảng 'categories_blog'
        Schema::create('categories_blog', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Tạo bảng 'blogs'
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('category_id');
            $table->boolean('status')->default(true); // true: active, false: inactive
            $table->string('image_path')->nullable();
            $table->date('published_date')->nullable();
            $table->timestamps();

            // Thiết lập khóa ngoại tới bảng 'categories_blog'
            $table->foreign('category_id')->references('id')->on('categories_blog')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
