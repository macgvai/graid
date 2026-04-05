<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    // Настройка сообщений об ошибках (опционально)
    public function messages(): array
    {
        return [
            'login.required' => 'Поле "Логин" обязательно.',
            'email.required' => 'Поле "Email" обязательно.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Такой email уже зарегистрирован.',
            'password.required' => 'Поле "Пароль" обязательно.',
            'password.min' => 'Пароль должен быть не менее 6 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'avatar.image' => 'Файл аватара должен быть изображением.',
            'avatar.max' => 'Файл аватара не должен превышать 2 МБ.',
        ];
    }
}
