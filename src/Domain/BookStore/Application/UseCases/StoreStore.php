<?php

namespace Domain\Auth\Application\UseCases;

use Domain\BookStore\Infraestructure\Repositories\Contracts\StoreRepositoryInterface;

class StoreStore
{
    private StoreRepositoryInterface $repository;

    public function __construct(StoreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * store a book
     */
    public function execute($data)
    {
        return $this->repository->create($data);
    }
}
