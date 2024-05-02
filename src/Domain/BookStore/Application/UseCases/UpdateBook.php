<?php

namespace Domain\Auth\Application\UseCases;

use Domain\BookStore\Infraestructure\Repositories\Contracts\BookRepositoryInterface;

class UpdateBook
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * store a book
     */
    public function execute($id, $data)
    {
        return $this->repository->update($id, $data);
    }
}
