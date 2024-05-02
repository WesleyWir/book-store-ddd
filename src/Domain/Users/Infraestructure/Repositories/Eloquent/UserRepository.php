<?php

namespace Domain\Users\Infraestructure\Repositories\Eloquent;

use Domain\Users\Infraestructure\EloquentModels\User;
use Common\Infraestructure\Repositories\Eloquent\AbstractRepository;
use Domain\Users\Infraestructure\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->model = $user;
        $this->params = request()->all();
    }
}
