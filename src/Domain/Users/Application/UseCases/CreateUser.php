<?php

namespace Domain\Users\Application\UseCases;

use Domain\Users\Infraestructure\Repositories\Contracts\UserRepositoryInterface;


class CreateUser
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($data){
        return $this->repository->create($data);
    }
}