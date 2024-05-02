<?php

namespace Domain\Auth\Application\UseCases;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class Reset
{
    /**
     * forgot password
     * @return  string
     */
    public function execute($payload): mixed
    {
        return Password::reset(
            $payload,
            function ($user, string $password) {
                $user->forceFill([
                    'password' => $password
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );;
    }
}
