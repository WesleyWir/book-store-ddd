<?php

namespace Domain\Auth\Application\UseCases;

use Illuminate\Support\Facades\Password;

class Forgot
{
    /**
     * forgot password
     * @return  string
     */
    public function execute($email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }
}
