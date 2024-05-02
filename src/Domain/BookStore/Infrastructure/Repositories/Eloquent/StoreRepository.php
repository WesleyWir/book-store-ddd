<?php

namespace Domain\BookStore\Infraestructure\Repositories\Eloquent;

use Domain\BookStore\Infrastructure\EloquentModels\Book;
use Common\Infraestructure\Repositories\Eloquent\AbstractRepository;
use Domain\BookStore\Infraestructure\Repositories\Contracts\StoreRepositoryInterface;

class StoreRepository extends AbstractRepository implements StoreRepositoryInterface
{
    public function __construct(Book $book)
    {
        $this->model = $book;
        $this->params = request()->all();
    }
}
