<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMetaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'size' => $this->size ? $this->size->name : null,
            'color' => $this->color ? $this->color->name : null,
            'quantity' => $this->quantity,
        ];
    }
}
