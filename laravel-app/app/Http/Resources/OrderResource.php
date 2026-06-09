<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total_price,
            'customer_name' => $this->customer_name,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_css' => $this->status->cssClass(),

            'items' => $this->whenLoaded('items', function () {
                return OrderItemResource::collection($this->items->values());
            }),
            'created_at_display' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
