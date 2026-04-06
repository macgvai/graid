<?php

namespace App\Http\Requests;

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
            'login' => 'required|string|max:255|unique:users,login',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Поле «Логин» обязательно для заполнения.',
            'login.unique' => 'Пользователь с таким логином уже существует.',
            'email.required' => 'Поле «Электронная почта» обязательно для заполнения.',
            'email.email' => 'Укажите корректный email-адрес.',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',
            'password.required' => 'Поле «Пароль» обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать не менее 6 символов.',
            'password.confirmed' => 'Пароль и его повтор должны совпадать.',
            'avatar.image' => 'Аватар должен быть изображением.',
            'avatar.max' => 'Размер аватара не должен превышать 2 МБ.',
        ];
    }
}
