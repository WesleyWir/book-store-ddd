<?php

namespace Common\Infraestructure\Repositories;

use Illuminate\Contracts\Foundation\Application;

class BindRepositories
{
    private $repositoriesToBind = [
        'Users' => 'User',
    ];

    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bind()
    {
        foreach ($this->repositoriesToBind as $domain => $repo) {
            $this->app->bind(
                "Domain\\".$domain.'\\Infraestructure\Repositories\Contracts\\' . $repo . 'RepositoryInterface',
                'Domain\\'.$domain.'\\Infraestructure\Repositories\Eloquent\\' . $repo . 'Repository'
            );
        }
    }
}
