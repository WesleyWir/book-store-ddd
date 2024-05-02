<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Domain\BookStore\Presentation\Http\Controllers',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::apiResource('books', 'BookController');
    Route::apiResource('stores', 'StoreController');
});
