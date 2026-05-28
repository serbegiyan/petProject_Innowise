<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class BasketStoreRequest extends AuthenticatedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->input('product_id');

        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'services' => ['nullable', 'array'],
            'services.*.id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('product_service', 'service_id')
                    ->where(fn ($query) => $query->where('product_id', $productId)),
            ],
            'edit_cart_id' => ['nullable', 'integer', 'exists:baskets,id',
                Rule::exists('baskets', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
