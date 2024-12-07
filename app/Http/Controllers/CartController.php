<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\ProductMeta;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_meta,product_id',
            'color_id' => 'required|exists:product_meta,color_id',
            'size_id' => 'required|exists:product_meta,size_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();


        $productMeta = ProductMeta::where('product_id', $request->product_id)
                                  ->where('color_id', $request->color_id)
                                  ->where('size_id', $request->size_id)
                                  ->first();

        if (!$productMeta) {
            return response()->json(['error' => 'Product with the selected color and size is not available.'], 404);
        }

        $cartItem = CartItem::where('user_id', $user->id)
                    ->where('product_meta_id', $productMeta->id)
                    ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'product_meta_id' => $productMeta->id,
                'quantity' => $request->quantity,
                'price' => $productMeta->product->price,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully.']);
    }

    // Xem giỏ hàng
   // Xem giỏ hàng
   public function viewCart()
   {
       // Kiểm tra xác thực người dùng
       $user = auth()->user();
       if (!$user) {
           return response()->json(['message' => 'User not authenticated'], 401);
       }

       // Lấy các sản phẩm trong giỏ hàng với quan hệ cần thiết
       $cartItems = CartItem::with([
       'productMeta.color'=> function ($query){
           $query->select('id','name');
       },
       'productMeta.size'=>function ($query){
           $query->select('id','name');
       },
       'productMeta.product' => function ($query) {
           $query->select('id', 'name', 'image_path');
       }])
       ->where('user_id', $user->id)
       ->get();

       // Trả về dữ liệu qua Resource để chuẩn hóa
       return CartItemResource::collection($cartItems);
       // return response()->json($cartItems);
   }


    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart($product_meta_id)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    $cartItem = CartItem::where('user_id', $user->id)
                        ->where('product_meta_id', $product_meta_id)
                        ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Product not found in cart.'], 404);
    }

    $cartItem->delete();

    return response()->json();
}


    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateQuantity($product_meta_id, $quantity)
    {
        // Xác thực tham số
        if (!is_numeric($quantity) || $quantity < 1) {
            return response()->json(['error' => 'Invalid quantity.'], 400);
        }
    
        // Lấy người dùng hiện tại
        $user = auth()->user();
    
        // Tìm mục giỏ hàng (CartItem)
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_meta_id', $product_meta_id)
                            ->first();
    
        // Nếu tìm thấy, cập nhật số lượng
        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
            return response()->json(['message' => 'Cart item quantity updated successfully.']);
        }
    
        // Nếu không tìm thấy mục giỏ hàng, trả về lỗi
        return response()->json(['error' => 'Cart item not found.'], 404);
    }
    // Xóa tất cả sản phẩm trong giỏ hàng
    public function clearCart(Request $request)
    {
        $user = $request->user();

        CartItem::where('user_id', $user->id)->delete();

        return response()->json();
    }


}
