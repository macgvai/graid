<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Неверный email или пароль'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function register(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();


        $user = User::create([
            'login' => $validated['login'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return redirect()->route('login')
            ->with('status', 'Аккаунт успешно зарегистрирован! Добро пожаловать.');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Отзываем текущий токен
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Токен отозван'
        ]);
    }
}
