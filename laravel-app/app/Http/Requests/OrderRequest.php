<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|min:2|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string|min:10|max:500',
            'payment_method' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(array_keys(Order::STATUSES))],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Укажите, как к вам обращаться',
            'customer_phone.required' => 'Укажите номер телефона',
            'customer_email.required' => 'Укажите email',
            'customer_address.required' => 'Нам нужен адрес для доставки',
        ];
    }
}
