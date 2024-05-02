<?php

namespace Domain\Auth\Application\UseCases;

use Domain\BookStore\Infraestructure\Repositories\Contracts\BookRepositoryInterface;

class StoreBook
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
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
