<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Phương thức thanh toán
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    // Phương thức thanh toán
    public function checkout(Request $request)
    {
        // Xác thực thông tin yêu cầu
        $request->validate([
            'phone_number' => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:cod,momo', // 'cod' for cash on delivery, 'momo' for Momo payment
            'name_coupon' => 'nullable|string',
        ]);

        // Lấy người dùng đang đăng nhập
        $user = auth()->user();
        // Kiểm tra nếu giỏ hàng trống
        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        // Tính tổng giá
        $subTotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        $salePrice = 0;
        $totalPrice = $subTotal;
        $totalPrice = round($totalPrice, 0);
        $payment_method = $request->input('payment_method');
        // xử lý mã giảm
        $nameCoupon = $request->input('name_coupon');
        if ($nameCoupon) {
            $coupon = Coupon::where('code', $nameCoupon)->first();
            if ($coupon) {
                $couponType = $coupon->type;
                $couponValue = $coupon->coupon_value;
                if ($couponType === 'percent') {
                    $salePrice = $subTotal * ($couponValue / 100);
                    $totalPrice = $subTotal - $salePrice;
                } elseif ($couponType === 'fixed') {
                    $salePrice = min($couponValue, $subTotal);
                    $totalPrice = $subTotal - $salePrice;
                }
            } else {
                return response()->json(['message' => 'Mã giảm giá không hợp lệ'], 404);
            }
        }

        // Tạo đơn hàng mới
        $order = Order::create([
            'user_id' => $user->id,
            'phone_number' => $request->phone_number,
            'shipping_address' => $request->shipping_address,
            'total_price' => $totalPrice,
            'status' => 'Pending',
            'payment_method' => $payment_method,
            'sub_total' => $subTotal,
            'sale_price' => $salePrice,
            'name_coupon' => $nameCoupon,
        ]);

        // Tạo các item cho đơn hàng từ giỏ hàng
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_meta_id' => $cartItem->product_meta_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }

        // Xóa các item trong giỏ hàng sau khi thanh toán
        $cartItems->each->delete();

        // Kiểm tra phương thức thanh toán
        if ($request->payment_method === 'momo') {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = 'MOMOBKUN20180529';
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

            $orderInfo = "Thanh toán qua MoMo";
            $amount = $totalPrice;
            $orderId = rand(0, 9999);
            $redirectUrl = "http://localhost:3000/";
            $ipnUrl = "http://localhost:3000/ipn";
            $extraData = "";

            $requestId = time() . "";
            $requestType = "payWithMethod";


            // Tạo chữ ký
            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);

            $data = [
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                "storeId" => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            ];

            // Gửi yêu cầu tới MoMo
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            // Kiểm tra phản hồi từ MoMo
            if (isset($jsonResult['payUrl'])) {
                return response()->json(['payUrl' => $jsonResult['payUrl']]);
            } else {
                // Kiểm tra xem có thông tin lỗi từ phản hồi của MoMo không
                $errorMessage = isset($jsonResult['message']) ? $jsonResult['message'] : 'Unknown error';
                $errorCode = isset($jsonResult['resultCode']) ? $jsonResult['resultCode'] : 'No code';

                // Trả về thông tin lỗi chi tiết hơn
                return response()->json([
                    'error' => 'Could not create MoMo payment.',
                    'errorMessage' => $errorMessage,
                    'errorCode' => $errorCode
                ], 500);
            }
        }

        // Trả về thông tin đơn hàng cho phương thức thanh toán 'cod'
        return response()->json($order);
    }
     // Phương thức lấy thông tin đơn hàng
     public function getOrder($id)
     {
        $user = auth()->user();
         $order = Order::with('orderItems')->find($id);

         if (!$order) {
             return response()->json(['error' => 'Order not found.'], 404);
         }

         return response()->json($order);
     }
     public function get_all_order(){
        $user =auth()->user();
        $get_orders = Order::all();
        return response()->json($get_orders);
     }
     public function lay_donhang()
     {
        $user =auth()->user();
         $lay_donhang = Order::with([
             'users:id,name',
             'orderItems.productMeta.product:id,name', // Chỉ lấy `id` và `name` từ bảng `product`
         ])->get();

         return OrderResource::collection($lay_donhang);
     }
     // Phương thức xóa đơn hàng
     public function deleteOrder($id)
     {
        $user = auth()->user();
         $order = Order::find($id);

         if (!$order) {
             return response()->json(['error' => 'Order not found.'], 404);
         }

         // Xóa các item trong đơn hàng trước khi xóa đơn hàng
         OrderItem::where('order_id', $id)->delete();

         // Xóa đơn hàng
         $order->delete();

         return response()->json(['message' => 'Order deleted successfully.']);
     }
}
