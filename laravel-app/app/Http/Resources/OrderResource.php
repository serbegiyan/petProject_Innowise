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
        /** @var Order $order */
        $order = $this->resource;

        return [
            'id' => $order->id,
            'total' => $order->total_price,

            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'status_css' => $order->status->cssClass(),

            'items' => $order->items,
            'created_at' => $order->created_at->format('d.m.Y H:i'),
        ];
    }
}
