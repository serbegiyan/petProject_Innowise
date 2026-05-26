<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total_price,

            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_css' => $this->status->cssClass(),

            'items' => $this->items, // Здесь тоже в идеале сделать ItemResource
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
