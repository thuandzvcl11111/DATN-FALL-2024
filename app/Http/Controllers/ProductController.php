<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Size;
use App\Models\Color;
use App\Models\Category;
use App\Models\ProductMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
 // POST /post_product
//  public function post_product(Request $request)
// {
//     // Validate dữ liệu
//     $user = auth()->user();
//     try {
//         DB::beginTransaction();

//         // Xử lý ảnh base64 nếu có
//         $imagePath = null;
//         if ($request->has('image') && $request->input('image')) {
//             $imageData = $request->input('image');
//             $imageParts = explode(';', $imageData);
//             $imageExtension = explode('/', $imageParts[0])[1];
//             $imageBase64 = explode(',', $imageParts[1])[1];

//             $imageName = uniqid() . '.' . $imageExtension;
//             $imagePath = 'products/' . $imageName;

//             Storage::disk('public')->put($imagePath, base64_decode($imageBase64));

//             if (!$imagePath) {
//                 return response()->json(['message' => 'Failed to upload image'], 422);
//             }
//         }

//         // Tạo sản phẩm
//         $product = Product::create([
//             'name' => $request->name,
//             'description' => $request->description,
//             'price' => $request->price,
//             'category_id' => $request->category_id,
//             'is_hot' => $request->is_hot ?? false,
//             'is_new' => $request->is_new ?? false,
//             'status' => $request->status ?? 'in_stock',
//             'image_path' => $imagePath,  // biến đường dẫn ảnh
//         ]);

//         $arrColor = $request->input('colors', []);
//         $arrSize = $request->input('sizes', []);
//         $quantity = $request->input('quantity', 1);
//         $arrColorIds = [];
//         $arrSizeIds = [];

//         // Tạo màu sắc
//         foreach ($arrColor as $value) {
//             $resultColor = Color::create([
//                 'name' => $value,
//             ]);
//             $arrColorIds[] = $resultColor->id;
//         }

//         // Tạo kích thước
//         foreach ($arrSize as $value1) {
//             $resultSize = Size::create([
//                 'name' => $value1,
//             ]);
//             $arrSizeIds[] = $resultSize->id;
//         }

//         // Liên kết màu và kích thước với sản phẩm
//         $metaResults = [];
//         foreach ($arrColorIds as $colorId) {
//             foreach ($arrSizeIds as $sizeId) {
//                 $metaResults[] = ProductMeta::create([
//                     'product_id' => $product->id,
//                     'color_id' => $colorId,
//                     'size_id' => $sizeId,
//                     'quantity'=>$quantity,
//                 ]);
//             }
//         }

//         DB::commit();
//         return response()->json($metaResults);

//     } catch (\Exception $e) {
//         DB::rollBack();

//         // Xóa hình ảnh nếu có lỗi
//         if (isset($imagePath)) {
//             Storage::disk('public')->delete($imagePath);
//         }

//         Log::error('Product creation failed: ' . $e->getMessage());

//         return response()->json([
//             'message' => 'Error creating product',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
public function post_product(Request $request)
{
    $user = auth()->user();

    try {
        DB::beginTransaction();

        // Lấy chuỗi URL hình ảnh từ request
        $imagePath = $request->input('image'); // URL của ảnh từ mạng

        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_hot' => $request->is_hot ? 1 : 0,
            'is_new' => $request->is_new ? 1 : 0,
            'status' => $request->status ?? 'in_stock',
            'image_path' => $imagePath, // Lưu URL ảnh vào cơ sở dữ liệu
        ]);

        // Xử lý màu sắc và kích cỡ
        $arrColor = is_array($request->input('colors')) ? $request->input('colors') : explode(',', $request->input('colors', ''));
        $arrSize = is_array($request->input('sizes')) ? $request->input('sizes') : explode(',', $request->input('sizes', ''));
        $quantity = $request->input('quantity', 1);
        $arrColorIds = [];
        $arrSizeIds = [];

        // Tạo màu sắc nếu chưa tồn tại
        foreach ($arrColor as $value) {
            $color = Color::firstOrCreate(['name' => $value]);
            $arrColorIds[] = $color->id;
        }

        // Tạo kích cỡ nếu chưa tồn tại
        foreach ($arrSize as $value) {
            $size = Size::firstOrCreate(['name' => $value]);
            $arrSizeIds[] = $size->id;
        }

        // Liên kết màu sắc và kích cỡ với sản phẩm
        $metaResults = [];
        foreach ($arrColorIds as $colorId) {
            foreach ($arrSizeIds as $sizeId) {
                $metaResults[] = ProductMeta::create([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                ]);
            }
        }

        DB::commit();
        return response()->json($metaResults);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Product creation failed: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error creating product',
            'error' => $e->getMessage()
        ], 500);
    }
}



    // GET /get_all_product
    public function get_all_product()
    {
            $user = auth()->user();
            $products = Product::with('category', 'productMeta.size', 'productMeta.color')->get();
            return response()->json($products);
    }

    // GET /get_product_detail/{id}
    public function get_product_detail($id)
    {
        try {
            $product = Product::with('category', 'productMeta.size', 'productMeta.color')->find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error fetching product details: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching product', 'error' => $e->getMessage()], 500);
        }
    }

    // DELETE /delete_product/{id}
    public function delete_product($id)
    {
        $user = auth()->user();
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting product', 'error' => $e->getMessage()], 500);
        }
    }

    // PUT /put_product/{id}
    public function put_product(Request $request, $id)
{
    $user = auth()->user();
    // Validate dữ liệu đầu vào
    $validatedData = $request->validate([
        'name' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'price' => 'sometimes|numeric|min:0',
        'category_id' => 'sometimes|exists:categories,id',
        'image' => 'nullable|string',
        'is_hot' => 'boolean',
        'is_new' => 'boolean',
        'status' => 'in:in_stock,out_of_stock,pre_order',
        'colors' => 'nullable|array',
        'sizes' => 'nullable|array',
        'product_meta' => 'nullable|array',
        'product_meta.*.color_name' => 'sometimes|string',
        'product_meta.*.size_name' => 'sometimes|string',
        'product_meta.*.quantity' => 'required|integer|min:0',
    ]);

    try {
        DB::beginTransaction();

        // Tìm sản phẩm theo ID
        $product = Product::findOrFail($id);

        // Cập nhật các trường dữ liệu khác
        $product->name = $validatedData['name'] ?? $product->name;
        $product->description = $validatedData['description'] ?? $product->description;
        $product->price = $validatedData['price'] ?? $product->price;
        $product->category_id = $validatedData['category_id'] ?? $product->category_id;
        $product->is_hot = $validatedData['is_hot'] ?? $product->is_hot;
        $product->is_new = $validatedData['is_new'] ?? $product->is_new;
        $product->status = $validatedData['status'] ?? $product->status;

        // Xử lý ảnh nếu có
        if ($request->has('image') && $request->input('image')) {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $imageData = $request->input('image');
            $imageParts = explode(';', $imageData);
            if (count($imageParts) > 1) {
                $mimeParts = explode('/', $imageParts[0]);
                $base64Parts = explode(',', $imageParts[1]);

                if (count($mimeParts) > 1 && count($base64Parts) > 1) {
                    $imageExtension = $mimeParts[1];
                    $imageBase64 = $base64Parts[1];

                    $imageName = uniqid() . '.' . $imageExtension;
                    $imagePath = 'products/' . $imageName;

                    Storage::disk('public')->put($imagePath, base64_decode($imageBase64));
                    $product->image_path = $imagePath;
                } else {
                    return response()->json(['message' => 'Invalid image format'], 422);
                }
            } else {
                return response()->json(['message' => 'Invalid image format'], 422);
            }
        }

        $product->save();

        if (!empty($validatedData['product_meta'])) {
            foreach ($validatedData['product_meta'] as $meta) {
                // Tìm hoặc tạo màu sắc và kích thước
                $color = Color::firstOrCreate(['name' => $meta['color_name']]);
                $size = Size::firstOrCreate(['name' => $meta['size_name']]);

                // Kiểm tra xem product_meta đã tồn tại chưa
                $productMeta = ProductMeta::where('product_id', $product->id)
                    ->where('color_id', $color->id)
                    ->where('size_id', $size->id)
                    ->first();

                if ($productMeta) {
                    // Nếu tồn tại, cập nhật số lượng
                    $productMeta->quantity = $meta['quantity'];
                    $productMeta->save();
                } else {
                    // Nếu không tồn tại, tạo mới
                    ProductMeta::create([
                        'product_id' => $product->id,
                        'color_id' => $color->id,
                        'size_id' => $size->id,
                        'quantity' => $meta['quantity'],
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
            'product_meta' => $product->productMeta
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        if (isset($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        Log::error('Error updating product: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error updating product',
            'error' => $e->getMessage()
        ], 500);
    }
}




public function get_products_by_category($categoryId)
{
    try {
        $products = Product::with('category', 'productMeta.size', 'productMeta.color')
            ->where('category_id', $categoryId)
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found in this category'], 404);
        }

        return response()->json( $products);
    } catch (\Exception $e) {
        Log::error('Error fetching products by category: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error fetching products by category',
            'error' => $e->getMessage(),
        ], 500);
    }
}

// GET /products/hot
public function get_hot_products()
{
    try {
        $hotProducts = Product::with('category', 'productMeta.size', 'productMeta.color')
            ->where('is_hot', true)
            ->get();

        if ($hotProducts->isEmpty()) {
            return response()->json(['message' => 'No hot products found'], 404);
        }

        return response()->json($hotProducts);
    } catch (\Exception $e) {
        Log::error('Error fetching hot products: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error fetching hot products',
            'error' => $e->getMessage(),
        ], 500);
    }
}

// GET /products/new
public function get_new_products()
{
    try {
        $newProducts = Product::with('category', 'productMeta.size', 'productMeta.color')
            ->where('is_new', true)
            ->get();

        if ($newProducts->isEmpty()) {
            return response()->json(['message' => 'No new products found'], 404);
        }

        return response()->json($newProducts);
    } catch (\Exception $e) {
        Log::error('Error fetching new products: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error fetching new products',
            'error' => $e->getMessage(),
        ], 500);
    }
}
// tìm kiếm sản phẩm-----------------------------------------------------------------------------------------
public function search(Request $request)
{
    $query = $request->input('query');
    if (!$query) {
        return response()->json([
            'message' => 'Vui lòng nhập từ khóa tìm kiếm.',
        ]);
    }
    $product = Product::where('name', 'LIKE', "%{$query}%")->get();
    if ($product->isEmpty()) {
        return response()->json([
            'message' => 'Không tìm thấy sản phẩm có tên ' . $query,
        ]);
    }
    return response()->json($product);
}


}
