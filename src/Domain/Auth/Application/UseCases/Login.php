<?php

namespace Domain\Auth\Application\UseCases;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;

class Login
{
    private $request;

    public function __construct(
        Request $request,
    ) {
        $this->request = $request;
    }

    /**
     * Login
     * @param   mixed  $payload
     * @return  Authenticatable|false
     */
    public function execute($payload): Authenticatable|false
    {
        $email = $payload['email'];
        $password = $payload['password'];
        $credentials = ['email' => $email, 'password' => $password];
        if (Auth::guard()->attempt($credentials)) {
            session()->regenerate();
            return Auth::guard()->user();
        }
        return false;
    }
}
