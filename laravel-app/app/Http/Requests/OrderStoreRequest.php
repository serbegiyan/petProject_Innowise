<?php

namespace App\Http\Requests;

class OrderStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:2', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^(\+?[0-9\s\-\(\)]+)$/', 'min:10', 'max:20'],
            'customer_address' => ['required', 'string', 'min:10', 'max:500'],
            'payment_method' => ['nullable', 'string', 'in:card,cash'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Укажите, как к вам обращаться',
            'customer_phone.required' => 'Укажите номер телефона',
            'customer_phone.regex' => 'Неверный формат номера телефона',
            'customer_email.required' => 'Укажите email',
            'customer_address.required' => 'Нам нужен адрес для доставки',
        ];
    }
}
