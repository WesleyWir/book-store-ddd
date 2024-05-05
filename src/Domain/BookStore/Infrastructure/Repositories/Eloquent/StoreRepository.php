<?php

namespace Domain\BookStore\Infraestructure\Repositories\Eloquent;

use Domain\BookStore\Infrastructure\EloquentModels\Store;
use Common\Infraestructure\Repositories\Eloquent\AbstractRepository;
use Domain\BookStore\Infraestructure\Repositories\Contracts\StoreRepositoryInterface;

class StoreRepository extends AbstractRepository implements StoreRepositoryInterface
{
    public function __construct(Store $store)
    {
        $this->model = $store;
        $this->params = request()->all();
    }
}
