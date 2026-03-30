<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ServiceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $serviceId = $this->route('service') ? $this->route('service')->id : null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($serviceId)],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services')->ignore($serviceId)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите название услуги.',
            'name.unique' => 'Услуга с таким названием уже существует.',
            'name.max' => 'Название услуги не должно превышать 255 символов.',
        ];
    }
}
