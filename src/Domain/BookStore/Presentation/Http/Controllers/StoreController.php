<?php

namespace Domain\BookStore\Presentation\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Domain\Auth\Application\UseCases\FindStore;
use Domain\Auth\Application\UseCases\StoreStore;
use Domain\Auth\Application\UseCases\DeleteStore;
use Domain\Auth\Application\UseCases\UpdateStore;
use Domain\BookStore\Presentation\Http\Resources\StoreResource;
use Domain\BookStore\Presentation\Http\Requests\Store\StoreStoreRequest;
use Domain\BookStore\Presentation\Http\Requests\Store\UpdateStoreRequest;
use Domain\BookStore\Infraestructure\Repositories\Contracts\StoreRepositoryInterface;

class StoreController extends Controller
{
    public function index(StoreRepositoryInterface $repository)
    {
        return StoreResource::collection($repository->list());
    }

    public function store(StoreStoreRequest $request, StoreRepositoryInterface $repository)
    {
        try {
            $entity = (new StoreStore($repository))->execute($request->all());
            return response(new StoreResource($entity), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    public function show($id, StoreRepositoryInterface $repository)
    {
        return new StoreResource((new FindStore($repository))->execute($id));
    }

    public function update(UpdateStoreRequest $request, $id, StoreRepositoryInterface $repository)
    {
        try {
            (new UpdateStore($repository))->execute($id, $request->all());
            return response([], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id, StoreRepositoryInterface $repository)
    {
        try {
            (new DeleteStore($repository))->execute($id);
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }
}
