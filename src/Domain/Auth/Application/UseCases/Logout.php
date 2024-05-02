<?php

namespace Domain\Auth\Application\UseCases;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Logout
     * @return null
     */
    public function execute()
    {
        Auth::guard()->logout();
        $this->request->session()->invalidate();
        $this->request->session()->regenerateToken();
        return;
    }
}
