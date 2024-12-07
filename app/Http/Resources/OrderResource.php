<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'user_id' => $this->user_id,
        'user_name' => $this->users ? $this->users->name : null, // Kiểm tra user trước khi lấy name
        'total_price' => $this->total_price,
        'payment_method' => $this->payment_method,
        'shipping_address' => $this->shipping_address,
        'phone_number' => $this->phone_number,
        'status' => $this->status,
        'products' => $this->orderItems->map(function ($item) {
            return [
                'product_name' => $item->productMeta->product->name,  // Lấy tên sản phẩm
                'quantity' => $item->quantity,                          // Lấy số lượng từ bảng order_items
                'price' => $item->price,                                // Lấy giá từ bảng order_items
                'total_price' => $item->quantity * $item->price,        // Tính tổng giá sản phẩm trong đơn hàng
            ];
        }),
    ];
}

}