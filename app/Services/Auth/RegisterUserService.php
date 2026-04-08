<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\UploadedFile;

class RegisterUserService
{
    public function __construct(
        private readonly Hasher $hasher,
    ) {
    }

    /**
     * @param  array{email:string,login:string,password:string}  $data
     */
    public function register(array $data, ?UploadedFile $avatar = null): User
    {
        $avatarPath = $avatar?->store('avatars', 'public');

        return User::query()->create([
            'email' => $data['email'],
            'login' => $data['login'],
            'password' => $this->hasher->make($data['password']),
            'avatar' => $avatarPath,
        ]);
    }
}
