<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /get_category - Lấy tất cả danh mục
    public function get_category()
    {
        return response()->json(Category::all());
    }
    public function get_category_withoutparentId()
    {
        // Lấy ra các categories có parent_id bằng null
        $categories = Category::whereNull('parent_id')->get();
    
        return response()->json($categories);
    }
    // GET /get_cate_detail/{id} - Lấy chi tiết một danh mục
    public function get_cate_detail($id)
    {
        $category = Category::find($id);
        return $category
            ? response()->json($category)
            : response()->json(['message' => 'Category not found'], 404);
    }

    // POST /post_cate - Tạo mới danh mục
    public function post_cate(Request $request)
{
    $user = auth()->user();
    // Validate the request data
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changed to accept an image file
        'mota' => 'nullable|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',

    ]);

    try {
        // Create a new category with name and description
        $category = new Category();
        $category->name = $request->name;
        $category->mota = $request->mota;
        $category->parent_id = $request->parent_id;

        // If there's an image file, handle the upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('/images/'), $filename);
            $category->image = $filename;
        }

        // Save the category to the database
        $category->save();

        // Return a successful JSON response
        return response()->json($category);
    } catch (\Exception $e) {
        // Return a JSON error response if something goes wrong
        return response()->json([
            'message' => 'Error creating category',
            'error' => $e->getMessage()
        ], 500);
    }
}
    // DELETE /delete_cate/{id} - Xóa danh mục
    public function delete_cate($id)
    {
        $user = auth()->user();
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
    public function put_cate(Request $request, $id)
{
    $user = auth()->user();

    // Validate the request data
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Allow an image file
        'mota' => 'nullable|string|max:255',
    ]);

    try {
        // Find the category by ID
        $category = Category::findOrFail($id);

        // Update the category's name and description
        $category->name = $request->name;
        $category->mota = $request->mota;
        

        // If there's an image file, handle the upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($category->image && file_exists(public_path('image/categories/' . $category->image))) {
                unlink(public_path('image/categories/' . $category->image));
            }

            // Upload the new image
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image/categories/'), $filename);
            $category->image = $filename;
        }

        // Save the updated category to the database
        $category->save();

        // Return a successful JSON response
        return response()->json($category);
    } catch (\Exception $e) {
        // Return a JSON error response if something goes wrong
        return response()->json([
            'message' => 'Error updating category',
            'error' => $e->getMessage()
        ], 500);
    }
}
//  đệ quy ca te
public function getNestedCategories($parentId = null)
{
    // Hàm đệ quy để lấy danh sách danh mục theo cấp
    $categories = Category::where('parent_id', $parentId)->get();
    $result = [];

    foreach ($categories as $category) {
        $categoryData = [
            'id' => $category->id,
            'name' => $category->name,
            'children' => $this->getNestedCategories($category->id), // Gọi đệ quy
        ];
        $result[] = $categoryData;
    }

    return $result;
}

// API để trả về danh sách danh mục dạng cây
public function getCategoriesTree()
{
    $categoriesTree = $this->getNestedCategories(); // Lấy tất cả danh mục gốc
    return response()->json(
       $categoriesTree
    );
}

}
