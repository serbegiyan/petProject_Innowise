<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'customer_name' => ['required', 'string', 'min:2', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'min:10'],
            'customer_address' => ['required', 'string', 'min:10'],
        ];
    }
}
