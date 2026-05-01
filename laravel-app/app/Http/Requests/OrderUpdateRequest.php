<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rules\Enum;

class OrderUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OrderStatus::class)],
            'customer_name' => ['required', 'string', 'min:2', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'min:10'],
            'customer_address' => ['required', 'string', 'min:10'],
        ];
    }
}
