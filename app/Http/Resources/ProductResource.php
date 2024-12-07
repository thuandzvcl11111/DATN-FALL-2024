<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'is_hot' => $this->is_hot,
            'is_new' => $this->is_new,
            'status' => $this->status,
            'active' => $this->active,
            'category_id' => $this->category_id,
            'image_path' => $this->image_path,
            'variants' => $this->productMeta->map(function ($meta) {
                return [
                    'size' => $meta->size ? $meta->size->name : null,
                    'color' => $meta->color ? $meta->color->name : null,
                    'quantity' => $meta->quantity,
                ];
            }),
        ];
    }
}
