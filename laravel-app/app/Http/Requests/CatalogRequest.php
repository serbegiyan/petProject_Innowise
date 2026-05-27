<?php

namespace App\Http\Requests;

use App\Enums\ProductSort;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

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

            // Используем автоматическую валидацию по Enum вместо хардкода строки 'in:...'
            'sort' => ['nullable', Rule::enum(ProductSort::class)],
        ];
    }
}
