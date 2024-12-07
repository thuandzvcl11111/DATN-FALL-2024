<?php
// app/Http/Controllers/CategoryBlogController.php

namespace App\Http\Controllers;

use App\Models\CategoryBlog;
use Illuminate\Http\Request;

class CategoryBlogController extends Controller
{
    // Liệt kê tất cả các categories
    public function show_cate_blog()
    {
        $categories = CategoryBlog::all();
        return response()->json($categories);
    }

    // Tạo một category mới
    public function add_category_blog(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $category = CategoryBlog::create([
                'name' => $request->name,
            ]);

            return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating category', 'error' => $e->getMessage()], 500);
        }
    }

    // Hiển thị chi tiết một category
    public function show_cate_blog_detail($id)
    {
        $category = CategoryBlog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    // Cập nhật một category
    public function update_cate_blog(Request $request, $id)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = CategoryBlog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        try {
            $category->update([
                'name' => $request->name,
            ]);

            return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating category', 'error' => $e->getMessage()], 500);
        }
    }

    // Xóa một category
    public function delete_cate_blog($id)
    {
        $user = auth()->user();
        $category = CategoryBlog::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        try {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting category', 'error' => $e->getMessage()], 500);
        }
    }
}
