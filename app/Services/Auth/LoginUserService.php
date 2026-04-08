<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

class LoginUserService
{
    public function __construct(
        private readonly AuthFactory $authFactory,
    ) {
    }

    /**
     * @param  array{email:string,password:string}  $credentials
     */
    public function authenticate(array $credentials): void
    {
        /** @var StatefulGuard $guard */
        $guard = $this->authFactory->guard('web');

        if (! $guard->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Неверный email или пароль.',
            ]);
        }
    }
}
