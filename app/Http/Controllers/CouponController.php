<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreCouponRequest;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CouponController extends Controller
{
        public function useCoupon(Request $request)
    {
        $user = auth()->user();
        // Validate that a coupon code has been provided
        $request->validate([
            'code' => 'required|string'
        ]);

        // Get the coupon code from the request
        $couponCode = $request->input('code');

        // Check if the coupon exists and is valid
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Mã giảm giá không tồn tại hoặc không hợp lệ'
            ], 404);
        }

        Session::put([
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'coupon_value' => $coupon->coupon_value,
            'cart_value' => $coupon->cart_value,
        ]);

        // $cart = CartItem::where('user_id', Auth::user()->id)->get();

        // Return a response with success message and cart data
        return response()->json([
            'message' => 'Áp dụng mã giảm giá thành công',
            // 'cart' => $cart,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->coupon_value,
                'cart_value' => $coupon->cart_value,
            ]
        ], 200);
    }

    public function unUseCoupon()

{
    // Lấy giỏ hàng của người dùng đã đăng nhập
    $cart = CartItem::where('user_id', Auth::user()->id)->get();
    // Lấy sản phẩm ngẫu nhiên để hiển thị gợi ý
    // $products = Product::inRandomOrder()->limit(4)->get();
    // Xóa thông tin mã giảm giá từ session
    Session::forget(['id', 'code', 'type', 'coupon_value', 'cart_value']);

    // Trả về phản hồi JSON với thông điệp thành công và dữ liệu giỏ hàng, sản phẩm gợi ý
    return response()->json([
        'message' => 'Hủy dùng mã giảm giá thành công',
        'cart' => $cart,
        'suggested_products' => $products
    ], 200);
}

    public function post_coupon(Request $request)
    {
        $coupon = new Coupon();
        $coupon->fill($request->except('_token'));
        $coupon->save();

        return response()->json($coupon);
    }
    public function get_all_coupon()
    {
        $coupons = Coupon::all();

        return response()->json($coupons);
    }

    public function update_coupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->fill($request->except('_token', '_method'));
        $coupon->save();

        return response()->json($coupon);
    }

    public function delete_coupon($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['error' => 'Mã giảm giá không tồn tại'], 404);
        }

        $coupon->delete();

        return response()->json([
            'message' => 'Xóa mã giảm giá thành công'
        ], 200);
    }
}
