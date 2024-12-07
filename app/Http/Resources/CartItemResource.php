<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'product_meta_id' => $this->product_meta_id,
            'product_name' => $this->productMeta->product->name,
            'color' => $this->productMeta->color->name,
            'size' => $this->productMeta->size->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->price * $this->quantity,
            'image_path'=> $this->productMeta->product->image_path,

        ];
    }
}
