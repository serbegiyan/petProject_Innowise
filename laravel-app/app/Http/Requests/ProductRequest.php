<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            // Основные поля продукта
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($productId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'brand' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'release_date' => ['nullable', 'date'],
            'price' => ['required', 'numeric', 'min:0'],

            // Валидация категории
            'category_id' => ['required', 'exists:categories,id'],

            // Валидация услуг
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],

            // Валидация цен и сроков для каждой выбранной услуги
            'service_prices' => ['nullable', 'array'],
            'service_prices.*' => ['nullable', 'numeric', 'min:0'],

            'service_terms' => ['nullable', 'array'],
            'service_terms.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите название товара.',
            'price.required' => 'Пожалуйста, введите цену товара.',
            'price.numeric' => 'Цена должна быть числом.',
            'image.max' => 'Файл слишком тяжелый (макс. 2МБ).',
            'image.mimes' => 'Недопустимый формат изображения. Разрешены: jpg, jpeg, png, webp.',
            'category_id.required' => 'Пожалуйста, выберите категорию товара.',
            'description.string' => 'Описание должно быть текстом.',
            'brand.required' => 'Пожалуйста, укажите бренд.',
            'release_date.date' => 'Дата производства должна быть в формате даты.',
        ];
    }
}
