<?php

namespace Kriss\WebmanAmisAdmin\Repository;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class EloquentRepository extends AbsRepository
{
    protected ?EloquentModel $model = null;
    protected string $modelClass;

    public function __construct($model)
    {
        $this->initModel($model);
    }

    /**
     * @inheritDoc
     */
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array
    {
        $query = $this->model()->newQuery();
        $query = $this->buildSearch($query, $search);
        $query = $this->buildOrder($query, $order);

        return $query
            ->paginate($perPage, $this->getGridColumns(), 'page', $page)
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function detail($id): array
    {
        return $this->model()->newQuery()
            ->find($id, $this->getDetailColumns())
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function create(array $attributes): void
    {
        $model = $this->model();
        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }
        $model->save();
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes, $id): void
    {
        $model = $this->model()->newQuery()->find($id, $this->getFormColumns());
        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }
        $model->save();
    }

    /**
     * @inheritDoc
     */
    public function destroy($id): void
    {
        $model = $this->model()->newQuery()->find($id);
        $model->delete();
    }

    /**
     * @inheritDoc
     */
    public function recovery($id): void
    {
        $this->model()->newQuery()->whereKey($id)->restore();
    }

    protected function initModel($model)
    {
        if (is_string($model)) {
            $this->modelClass = $model;
        } elseif ($model instanceof EloquentModel) {
            $this->modelClass = get_class($model);
            $this->model = $model;
        } else {
            throw new \InvalidArgumentException('model error');
        }
        $this->keyName = $this->model()->getKeyName();
    }

    protected function model(): EloquentModel
    {
        return $this->model ?: new $this->modelClass;
    }

    protected function buildSearch(EloquentBuilder $query, array $search): EloquentBuilder
    {
        $search = $this->filterByKey($search);
        return $query->where($search);
    }

    protected function buildOrder(EloquentBuilder $query, array $order): EloquentBuilder
    {
        foreach ($order as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query;
    }

    protected function filterByKey(array $search): array
    {
        $search = array_filter($search);
        $columns = $this->model()->getConnection()->getSchemaBuilder()->getColumnListing($this->model()->getTable());
        return array_filter($search, fn($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);
    }
}