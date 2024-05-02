<?php

namespace Common\Infraestructure\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

abstract class AbstractRepository
{
    protected $mainModel;
    protected $model;
    protected $params;
    protected $query;
    /**
     * The model foreignKey in other tables. ex: user_id, company_id
     * string
     */
    protected $modelForeignKey = '';
    protected $polymorphicModelRelations = [];
    /**
     * models and data to verify
     */
    protected $relatedModels = [];

    const DEFAULT_ITEMS_PER_PAGE = 10;

    protected function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    protected function setMainModel($model)
    {
        $this->mainModel = $model;
        return $this;
    }

    protected function appendToRelatedModels($model, $data = [])
    {
        array_push($this->relatedModels, [
            'model' => $model,
            'data' => $data
        ]);
    }

    protected function executeRelatedModels()
    {
        $model = $this->model;
        foreach ($this->relatedModels as $key => $item) {
            if (!isset($item['model']) || empty($item['data']))
                continue;
            $this->model = $item['model'];
            $this->updateOrCreateBelongsToRelationships($item['data']);
            $this->createOrUpdateRelation($item['data']);
            $this->model->update($item['data']);
            unset($this->relatedModels[$key]);
        }
        $this->model = $model;
    }

    protected function getQueryModel()
    {
        return $this->model::query();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function list()
    {
        $query = QueryBuilder::for($this->model);
        $allowedFilters = method_exists($this->model, 'allowedFilters') ? $this->model::allowedFilters() : [];
        $allowedSorts = method_exists($this->model, 'allowedSorts') ? $this->model::allowedSorts() : [];
        $allowedIncludes = method_exists($this->model, 'allowedIncludes') ? $this->model::allowedIncludes() : [];

        $query = $query->allowedFilters($allowedFilters);
        $query = $query->allowedSorts($allowedSorts);
        $query = $query->allowedIncludes($allowedIncludes);

        if (!(array_key_exists('per_page', request()->query())) || request()->query()['per_page'] == 0) {
            return $query->get();
        }

        return $query
            ->paginate(request()->get('per_page', self::DEFAULT_ITEMS_PER_PAGE))
            ->appends(request()->query());
    }

    public function find($id)
    {
        return $this->getQueryModel()->findOrFail($id);
    }

    public function findBy($field, $search)
    {
        return $this->getQueryModel()->where($field, $search)->first();
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $data = $this->updateOrCreateBelongsToRelationships($data);
            $this->model = $this->getQueryModel()->create($data);
            $this->setMainModel($this->model)->createOrUpdateRelation($data);
            $this->executeRelatedModels();
            DB::commit();
            return $this->mainModel;
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $this->model = $this->find($id);
            $this->setMainModel($this->model)->createOrUpdateRelation($data);
            $data = $this->updateOrCreateBelongsToRelationships($data);
            $this->executeRelatedModels();
            $this->mainModel->update($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    public function updateOrCreateBy($field, $value, array $data)
    {
        $find = $this->findBy($field, $value);
        if ($find) {
            return $this->update($find->id, $data);
        }
        return $this->create($data);
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $this->find($id)->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    public function deleteBy($field, $search)
    {
        DB::beginTransaction();
        try {
            $this->findBy($field, $search)->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    protected function updateOrCreateBelongsToRelationships($data)
    {
        $belongsToModelRelations = $this->model->belongsToModelRelations ?? [];
        foreach ($belongsToModelRelations as $relation_name => $foreignKey) {
            if (!isset($data[$relation_name]) || empty($data[$relation_name])) {
                $this->deleteBelongsToRelationship($relation_name);
                $data[$foreignKey] = null;
                continue;
            };

            $model = $this->prepareBelongsToRelationship($data, $relation_name, $relation_name);
            if ($model) {
                $data[$foreignKey] = $model->id;
                $this->appendToRelatedModels($model, $data[$relation_name]);
            }
        }
        return $data;
    }

    protected function prepareBelongsToRelationship($data, $relationship, $key)
    {
        $model = $this->model->{$relationship}()->first();
        if (!isset($data[$key]) || !is_array($data[$key])) {
            return $model;
        }
        $item = $data[$key];
        $model = $this->model->{$relationship}()->first();
        if (!$model) {
            return $this->model->{$relationship}()->create($data[$key]);
        }
        $model->update($item);
        return $model;
    }

    protected function deleteBelongsToRelationship($relationship)
    {
        $model = $this->model->{$relationship}()->first();
        if ($model)
            return $model->first()->delete();
    }

    protected function createOrUpdateRelation($data)
    {
        $modelRelations = $this->model->modelRelations ?? [];

        foreach ($this->polymorphicModelRelations as $relation_name => $relation) {
            $this->preparePolymorphicRelationship($data, $relation_name, $relation);
        }

        foreach ($modelRelations as $relation_name => $relation_field) {
            $relation_name = is_numeric($relation_name) ? $relation_field : $relation_name;
            $this->prepareRelationship($data, $relation_name, $relation_field);
        }
    }

    protected function prepareRelationship($data, $relationship, $key)
    {
        if (!isset($data[$key]) || !is_array($data[$key])) {
            return $this->model;
        }
        $foreignPivotKeyName = false;
        $relatedPivotKeyName = false;
        if (method_exists($this->model->{$relationship}(), 'getForeignPivotKeyName')) {
            $foreignPivotKeyName = $this->model->{$relationship}()->getForeignPivotKeyName();
        }
        if (method_exists($this->model->{$relationship}(), 'getRelatedPivotKeyName')) {
            $relatedPivotKeyName = $this->model->{$relationship}()->getRelatedPivotKeyName();
        }
        $isManyRelation = ($foreignPivotKeyName && $relatedPivotKeyName);
        $this->deleteItemsRelationship($data[$key], $relationship, $isManyRelation);
        if ($isManyRelation) {
            return $this->updateOrCreateMany($data[$key], $relationship);
        }
        if (count($data[$key]) == count($data[$key], COUNT_RECURSIVE)) {
            return $this->updateOrCreateOtherRelations($data[$key], $relationship);
        }
        foreach ($data[$key] as $item) {
            $this->updateOrCreateOtherRelations($item, $relationship);
        }
    }

    protected function updateOrCreateMany($data = [], $relationship)
    {
        $relationshipTable = $this->model->{$relationship}()->getTable();
        $idsToSync = [];
        foreach ($data as $i => $item) {
            if (is_numeric($item)) {
                array_push($idsToSync, $item);
                unset($data[$i]);
                continue;
            }
            $itemId = isset($item['id']) ? $item['id'] : null;
            $searched = $this->model->{$relationship}()->wherePivot("$relationshipTable.id", $itemId)->first();
            $data[$i] = $item;
            if ($searched) {
                $searched->update($item);
                $this->appendToRelatedModels($searched, $item);
                unset($data[$i]);
            }
        }
        $this->syncToRelation($this->model->{$relationship}(), $idsToSync);
        $data = array_values($data);
        $fileTypesKey = ['image', 'file', 'video'];
        foreach ($data as $i => $value) {
            foreach ($value as $j => $item) {
                if (in_array($j, $fileTypesKey)) {
                    $value = $value[$j];
                }
            };
            $data[$i] = $value;
        }
        $createds = $this->model->{$relationship}()->createMany($data);

        foreach ($createds as $i => $createdModel) {
            $createdData = array_merge($createdModel->toArray(), $data[$i]);
            $this->appendToRelatedModels($createdModel, $createdData);
        }
    }

    protected function syncToRelation($relation, $ids = [])
    {
        if (empty($ids))
            return;
        foreach ($ids as $i => $id) {
            $find = $relation->getRelated()->find($id);
            if (!($find))
                unset($ids[$i]);
        }
        $relation->sync($ids);
    }

    protected function updateOrCreateOtherRelations($data = [], $relationship)
    {
        $data['id'] = isset($data['id']) ? $data['id'] : null;
        $model = $this->model->{$relationship}()->updateOrCreate(["id" => intval($data['id'])], $data);
        $this->appendToRelatedModels($model, $data);
    }

    protected function preparePolymorphicRelationship($data, $relationship, $relations = [])
    {
        $existentMorphs = [];
        foreach ($relations as $relation) {
            if (!isset($data[$relationship][$relation->code])) {
                continue;
            }
            $item = $data[$relationship][$relation->code];
            if (empty($item)) {
                continue;
            }
            $item['id'] = isset($item['id']) ? $item['id'] : null;
            $morph = $this->updateOrCreateMorph($item['id'], $relation->model, $item, $relationship);
            array_push($existentMorphs, $morph->id);
        }
        $this->deleteEmptyPolymorphicRelationship($relationship, $existentMorphs);
    }

    private function updateOrCreateMorph($id = null, $model, $item, $relationship)
    {
        $active = $item['active'] ?? 1;
        if (!$id) {
            $created = call_user_func("$model::create", $item);
            $morphCreated = $created->{$relationship}()->create(
                [
                    $this->modelForeignKey => $this->model->id,
                    'active' => $active
                ]
            );
            return $morphCreated;
        }

        $searchedMorph = $this->model->{$relationship}()->find($id);
        if ($searchedMorph) {
            $searchedMorph->update(['active' => $active]);
            foreach ($searchedMorph->getRelations() as $relation) {
                if ((get_class($relation) == $model)) {
                    $relation->update($item);
                    return $relation;
                }
            }
        }
    }

    private function deleteItemsRelationship($items, $relationship, $isManyRelation = false)
    {
        if ($isManyRelation && $this->isArrayNumeric($items)) {
            // items just has numbers, and is relationship ManyToMany, 
            // so gonna use sync function in updateOrCreateMany.
            return;
        }
        $idsNow = array_filter(array_column($items, 'id'));
        $relationshipTable = false;
        if (method_exists($this->model->{$relationship}(), 'getTable')) {
            $relationshipTable = $this->model->{$relationship}()->getTable();
        }
        $idKey = $relationshipTable ? "$relationshipTable.id" : 'id';
        $idsOld = $this->model->{$relationship}()->pluck($idKey)->toArray();
        $idsToDelete = array_diff($idsOld, $idsNow);
        if (!empty($idsToDelete)) {
            $this->model->{$relationship}()->whereIn($idKey, $idsToDelete)->delete();
        }
    }

    private function isArrayNumeric($array)
    {
        foreach ($array as $element) {
            if (!is_numeric($element))
                return false;
        }
        return true;
    }

    private function deleteEmptyPolymorphicRelationship($relationship, $existentRelations)
    {
        $itemsToDelete = $this->model->{$relationship}()
            ->where($this->modelForeignKey, $this->model->id)
            ->whereNotIn('id', $existentRelations);
        $itemsToDelete->delete();
    }
}
