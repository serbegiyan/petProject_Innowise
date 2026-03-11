<?php

namespace App\Http\Requests;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$userId],
            'role' => ['required', 'string', 'max:255', 'in:user,admin'],
            'password' => $this->isMethod('post') ? ['required', 'string', 'min:8', 'confirmed'] : ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите имя пользователя.',
            'email.required' => 'Пожалуйста, введите email.',
            'email.email' => 'Пожалуйста, введите действительный email.',
            'email.unique' => 'Этот email уже используется.',
            'password.required' => 'Пожалуйста, введите пароль.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ];
    }
}
