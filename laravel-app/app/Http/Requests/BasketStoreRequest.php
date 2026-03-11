<?php

namespace App\Http\Requests;

class BasketStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'services' => ['nullable', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
        ];
    }
}
