<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class CatalogRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'sort' => ['nullable', 'string', 'in:price_asc,price_desc,release_asc,release_desc'],
        ];
    }
}
