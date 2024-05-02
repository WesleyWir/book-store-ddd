<?php

namespace App\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Common\Infraestructure\Repositories\BindRepositories;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        (new BindRepositories($this->app))->bind();
    }
}
