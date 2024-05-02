<?php

namespace Common\Infraestructure\Repositories\Contracts;

interface AbstractRepositoryInterface
{
    public function all();
    public function list();
    public function find($id);
    public function findBy($field, $search);
    public function create(array $data);
    public function update($id, array $data);
    public function updateOrCreateBy($field = '', $value, array $data);
    public function delete($id);
    public function deleteBy($field, $search);
}
