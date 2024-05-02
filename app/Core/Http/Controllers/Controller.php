<?php

namespace App\Core\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function catchResponseStatus($code)
    {
        $httpStatusCodeExist = array_key_exists($code, Response::$statusTexts);
        return $httpStatusCodeExist ? $code : 400;
    }
}
