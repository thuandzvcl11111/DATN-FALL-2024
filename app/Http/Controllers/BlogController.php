<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Models\Blog;
use App\Models\CategoryBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class BlogController extends Controller
{
    public function show_blog()
    {
        $blogs = Blog::with('category')->get();
        return response()->json($blogs);
    }

    public function post_blog(Request $request)
{
    $user = auth()->user();

    try {
        DB::beginTransaction();

        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories_blog,id',
            'status' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
            'image' => 'nullable|string', // Thay đổi để nhận URL thay vì tệp ảnh
            'published_date' => 'nullable|date',
        ]);

        // Tạo blog
        $blog = Blog::create([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'category_id' => $validatedData['category_id'],
            'status' => $validatedData['status'] ?? true,
            'is_hot' => $validatedData['is_hot'] ?? false,
            'published_date' => $validatedData['published_date'] ?? now(),
            'image_path' => $validatedData['image'] ?? null, // Lưu URL hình ảnh
        ]);

        DB::commit();

        // Trả về kết quả JSON
        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        // Log lỗi để kiểm tra sau
        Log::error('Blog creation failed: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error creating blog',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    

    public function show_blog_detail($id)
    {
        $blog = Blog::with('category')->find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }
        return response()->json($blog);
    }

    public function update_blog(Request $request, $id)
    {
        $user = auth()->user();
    
        try {
            Log::info('Update request data:', $request->all());
    
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|exists:categories_blog,id',
                'status' => 'nullable|boolean',
                'is_hot' => 'nullable|boolean',
                'image' => 'nullable|string', // Chuyển image thành một chuỗi URL
                'published_date' => 'nullable|date',
            ]);
    
            $blog = Blog::findOrFail($id);
    
            DB::beginTransaction();
    
            $blog->fill([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'category_id' => $validatedData['category_id'],
                'status' => $validatedData['status'] ?? $blog->status,
                'is_hot' => $validatedData['is_hot'] ?? $blog->is_hot,
                'published_date' => $validatedData['published_date'] ?? $blog->published_date,
                'image_path' => $validatedData['image'] ?? $blog->image_path, // Sử dụng chuỗi URL thay vì tải lên
            ]);
    
            $blog->save();
    
            DB::commit();
    
            return response()->json($blog);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating blog',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function delete_blog($id)
    {
        $user = auth()->user();
        try {
            $blog = Blog::findOrFail($id);

            DB::beginTransaction();

            if ($blog->image_path) {
                Storage::disk('public')->delete($blog->image_path);
            }

            $blog->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting blog',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getBlogsByCategory($categoryId)
    {
        // Lấy các blog theo category_id
        $category = CategoryBlog::find($categoryId);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        // Lấy các blog thuộc category này
        $blogs = $category->blogs; // Mối quan hệ đã khai báo trong model CategoryBlog

        return response()->json([
            'category' => $category->name,
            'blogs' => $blogs
        ]);
    }
    public function blogHot()
    {
        $hotBlogs = Blog::where('is_hot', 1)->get();
    
        // Kiểm tra nếu không có blog nào
        if ($hotBlogs->isEmpty()) {
            return response()->json([
                'message' => 'No hot blogs found'
            ], 404); // Trả về mã lỗi 404
        }
    
        // Trả về dữ liệu nếu có blog
        return response()->json(
         $hotBlogs
        );
    }
    

    public function search_name_blog(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'message' => 'Vui lòng nhập từ khóa tìm kiếm.',
            ]);
        }

        // Tìm kiếm sản phẩm theo tên
        $blog = Blog::where('title', 'LIKE', "%{$query}%")->get();

        // Debugging để kiểm tra nội dung $blog
        if ($blog->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy blog có tên ' . $query,
            ]);
        }

        return response()->json($blog);
    }



}
