<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
        $serviceId = $this->route('service') ? $this->route('service')->id : null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($serviceId)],
            'description' => 'nullable|string',
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
