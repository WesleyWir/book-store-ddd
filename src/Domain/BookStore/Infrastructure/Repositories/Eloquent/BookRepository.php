<?php

namespace Domain\BookStore\Infraestructure\Repositories\Eloquent;

use Domain\BookStore\Infrastructure\EloquentModels\Book;
use Common\Infraestructure\Repositories\Eloquent\AbstractRepository;
use Domain\BookStore\Infraestructure\Repositories\Contracts\BookRepositoryInterface;

class BookRepository extends AbstractRepository implements BookRepositoryInterface
{
    public function __construct(Book $book)
    {
        $this->model = $book;
        $this->params = request()->all();
    }
}
