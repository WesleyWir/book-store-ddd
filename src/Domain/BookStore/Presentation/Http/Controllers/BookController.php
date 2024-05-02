<?php

namespace Domain\BookStore\Presentation\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use Domain\Auth\Application\UseCases\FindBook;
use Symfony\Component\HttpFoundation\Response;
use Domain\Auth\Application\UseCases\StoreBook;
use Domain\Auth\Application\UseCases\DeleteBook;
use Domain\Auth\Application\UseCases\UpdateBook;
use Domain\BookStore\Presentation\Http\Resources\BookResource;
use Domain\BookStore\Presentation\Http\Requests\Book\StoreBookRequest;
use Domain\BookStore\Presentation\Http\Requests\Book\UpdateBookRequest;
use Domain\BookStore\Infraestructure\Repositories\Contracts\BookRepositoryInterface;

class BookController extends Controller
{
    public function index(BookRepositoryInterface $repository)
    {
        return BookResource::collection($repository->list());
    }

    public function store(StoreBookRequest $request, BookRepositoryInterface $repository)
    {
        try {
            $entity = (new StoreBook($repository))->execute($request->all());
            return response(new BookResource($entity), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    public function show($id, BookRepositoryInterface $repository)
    {
        return new BookResource((new FindBook($repository))->execute($id));
    }

    public function update(UpdateBookRequest $request, $id, BookRepositoryInterface $repository)
    {
        try {
            (new UpdateBook($repository))->execute($id, $request->all());
            return response([], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id, BookRepositoryInterface $repository)
    {
        try {
            (new DeleteBook($repository))->execute($id);
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }
}
