<?php

namespace Domain\Auth\Application\UseCases;

use Domain\BookStore\Infraestructure\Repositories\Contracts\StoreRepositoryInterface;

class DeleteStore
{
    private StoreRepositoryInterface $repository;

    public function __construct(StoreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * store a book
     */
    public function execute($id)
    {
        return $this->repository->delete($id);
    }
}
