<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryBlogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CouponController;

//all category
Route::get('/get_category', [CategoryController::class, 'get_category']);
Route::get('/get_category_withoutparentId', [CategoryController::class, 'get_category_withoutparentId']);
Route::get('/get_cate_detail/{id}', [CategoryController::class, 'get_cate_detail']);
// Route::get('/categories_dequy', [CategoryController::class, 'dequy_categories']);

// Route::get('/getCategoriesTree', [CategoryController::class, 'getCategoriesTree']);
//all color
Route::get('get_color', [ColorController::class, 'get_color']);
Route::get('get_color_detail/{id}', [ColorController::class, 'get_color_detail']);

//all size
Route::get('get_size', [SizeController::class, 'get_size']);
Route::get('get_size_detail/{id}', [SizeController::class, 'get_size_detail']);

//all product
    Route::get('/get_all_product', [ProductController::class, 'get_all_product']); // Lấy danh sách sản phẩm
    Route::get('/get_product_detail/{id}', [ProductController::class, 'get_product_detail']); // Lấy chi tiết sản phẩm
    Route::get('/products/category/{categoryId}', [ProductController::class, 'get_products_by_category']);
    Route::get('/products/hot', [ProductController::class, 'get_hot_products']);
    Route::get('/products/new', [ProductController::class, 'get_new_products']);
    // tìm kiếm sản phẩm theo tên
    Route::get('/search', [ProductController::class, 'search']);

//all user
    Route::get('/users/{id}', [AuthController::class, 'getUserDetail']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::get('/usersIndex', [AuthController::class, 'getAllUsers']);
//all blog
    Route::get('/show_blogs', [BlogController::class, 'show_blog']);  // GET all blogs
    Route::get('/blogs/{id}', [BlogController::class, 'show_blog_detail']);  // GET blog by ID


    Route::get('/categories_blog', [CategoryBlogController::class, 'show_cate_blog']);  // GET all categories
    Route::get('/categories_blog/{id}', [CategoryBlogController::class, 'show_cate_blog_detail']); // GET category by ID


    Route::get('/blog-hot', [BlogController::class, 'blogHot']);

Route::get('/category/{categoryId}/blogs', [BlogController::class, 'getBlogsByCategory']);
// tìm kiếm blog theo tên
Route::get('/search_blog', [BlogController::class, 'search_name_blog']);
//all contacts
    Route::post('/contacts', [ContactController::class, 'add_contact']);  // POST add new contact
    Route::get('/contacts', [ContactController::class, 'show_contact']);  // GET all contacts
    Route::get('/contacts/{id}', [ContactController::class, 'show_contact_id']); // GET contact by ID
//all order
//commet
Route::get('/getcomments', [CommentController::class, 'get_comment']);
//order
Route::get('/order/{id}', [OrderController::class, 'getOrder']);
Route::delete('/order/{id}', [OrderController::class, 'deleteOrder']);

// comment-----------------------
// coupon mã giảm giá-----------------------
Route::post('/add_coupon', [CouponController::class, 'post_coupon']);
Route::get('/get_all_coupon', [CouponController::class, 'get_all_coupon']);
Route::put('/update_coupon/{id}', [CouponController::class, 'update_coupon']);
Route::delete('/delete_coupon/{id}', [CouponController::class, 'delete_coupon']);
Route::post('/unUsecoupon', [CouponController::class, 'unUsecoupon']);
// Route::post('/useCoupon', [CouponController::class, 'useCoupon']);
Route::post('/useCoupon', [CouponController::class, 'useCoupon']);

Route::get('/get_all_order', [OrderController::class, 'get_all_order']);
// banner slide--------------------------
Route::get('/get_banner', [CouponController::class, 'get_banner']);
//admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    //category_product ==========
    Route::post('/post_cate', [CategoryController::class, 'post_cate']);
    Route::put('/put_cate/{id}', [CategoryController::class, 'put_cate']);
    Route::delete('/delete_cate/{id}', [CategoryController::class, 'delete_cate']);
    //color =======
    Route::post('post_color', [ColorController::class, 'post_color']);
    Route::put('put_color/{id}', [ColorController::class, 'put_color']);
    Route::delete('delete_color/{id}', [ColorController::class, 'delete_color']);
    //size =========================
    Route::post('post_size', [SizeController::class, 'post_size']);
    Route::put('edit_size/{id}', [SizeController::class, 'edit_size']);
    Route::delete('delete_size/{id}', [SizeController::class, 'delete_size']);
    //product ========================
    Route::post('/post_product', [ProductController::class, 'post_product']); // Tạo sản phẩm mới
    Route::delete('/delete_product/{id}', [ProductController::class, 'delete_product']); // Xóa sản phẩm
    Route::put('/put_product/{id}', [ProductController::class, 'put_product']); // Cập nhật sản phẩm
    //user===============================
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    //blog ============================
    Route::post('/post_blog', [BlogController::class, 'post_blog']);  // POST add new blog
    Route::put('/blogs/{id}', [BlogController::class, 'update_blog']); // PUT update blog
    Route::delete('/blogs/{id}', [BlogController::class, 'delete_blog']); // DELETE blog by ID
    //con tắc ===================
    Route::delete('/contacts/{id}', [ContactController::class, 'delete_concact']); // DELETE contact by ID
    //category blog ==================
    Route::post('/categories_blog', [CategoryBlogController::class, 'add_category_blog']);  // POST add new category
    Route::put('/categories_blog/{id}', [CategoryBlogController::class, 'update_cate_blog']); // PUT update category
    Route::delete('/categories_blog/{id}', [CategoryBlogController::class, 'delete_cate_blog']); // DELETE category by ID
    //order =
    // Route::get('/donhang', [OrderController::class, 'lay_donhang']);
    Route::get('/get_all_order', [OrderController::class, 'get_all_order']);
    Route::delete('/order/{id}', [OrderController::class, 'deleteOrder']);
    // user
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::delete('/users_delete/{id}', [AuthController::class, 'delete_user']);
});
// khách hàng
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
  // Thêm sản phẩm vào giỏ hàng
  Route::post('/cart', [CartController::class, 'addToCart']);

  // Xem giỏ hàng
  Route::get('/cart', [CartController::class, 'viewCart']);

  // Xóa sản phẩm khỏi giỏ hàng
  Route::delete('/cart/{product_meta_id}', [CartController::class, 'removeFromCart']);

  // Cập nhật số lượng sản phẩm trong giỏ hàng =
  Route::put('cart/update-quantity/{product_meta_id}/{quantity}', [CartController::class, 'updateQuantity']);


  // Xóa tất cả sản phẩm trong giỏ hàng =
  Route::delete('/cart/clear', [CartController::class, 'clearCart']);
  // order =
  Route::get('/order/{id}', [OrderController::class, 'getOrder']);


  Route::post('/checkout', [OrderController::class, 'checkout']);
  //comment =
    Route::post('/comments', [CommentController::class, 'post_comment']);
    Route::put('/comments/{id}', [CommentController::class, 'update_comment']);
    Route::delete('/comments/{id}', [CommentController::class, 'delete_comment']);
    // user
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

