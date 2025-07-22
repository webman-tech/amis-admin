<?php

namespace WebmanTech\AmisAdmin\Repository;

use Illuminate\Contracts\Pagination\Paginator as PaginatorInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EloquentRepository extends AbsRepository
{
    protected ?EloquentModel $model = null;
    /**
     * @var class-string<EloquentModel>
     */
    protected string $modelClass;
    protected ?array $defaultOrder = null;

    /**
     * @param EloquentModel|class-string<EloquentModel> $model
     */
    public function __construct($model)
    {
        $this->initModel($model);
        if ($this->defaultOrder === null) {
            $this->defaultOrder = [
                // 默认按照主键倒序
                $this->model()->qualifyColumn($this->model()->getKeyName()) => 'desc',
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey ?: $this->model()->getKeyName();
    }

    /**
     * @param EloquentModel|class-string<EloquentModel> $model
     */
    protected function initModel($model): void
    {
        if (is_string($model)) {
            $this->modelClass = $model;
        } elseif ($model instanceof EloquentModel) {
            $this->modelClass = $model::class;
            $this->model = $model;
        } else {
            throw new \InvalidArgumentException('model error');
        }
        $this->primaryKey = $this->model()->getKeyName();
    }

    /**
     * @return EloquentModel
     */
    public function model(): EloquentModel
    {
        if (!$this->model) {
            $this->model = new $this->modelClass;
        }
        return $this->model;
    }

    /**
     * @return EloquentBuilder
     */
    public function query(): EloquentBuilder
    {
        return $this->model()->newQuery();
    }

    /**
     * @inheritDoc
     */
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array
    {
        $query = $this->query();
        $query = $this->buildSearch($query, $search);
        $query = $this->buildOrder($query, $order);
        $query = $this->extGridQuery($query);

        $paginator = $this->queryPagination($query, $perPage, $page);
        if ($paginator instanceof AbstractPaginator) {
            /** @var DBCollection $itemCollection */
            $itemCollection = $paginator->getCollection();
        } elseif ($paginator instanceof DBCollection) {
            $itemCollection = $paginator;
        } else {
            throw new \InvalidArgumentException('error $paginator type');
        }
        $itemCollection
            ->makeHidden($this->hiddenAttributes(static::SCENE_LIST))
            ->makeVisible($this->visibleAttributes(static::SCENE_LIST))
            ->append($this->appendAttributes(static::SCENE_LIST));

        return $this->solvePaginationResult($paginator);
    }

    /**
     * 构建搜索条件
     * @param EloquentBuilder $query
     * @param array $search
     * @return EloquentBuilder
     */
    protected function buildSearch(EloquentBuilder $query, array $search): EloquentBuilder
    {
        $searchableAttributes = $this->searchableAttributes();
        foreach ($search as $attribute => $value) {
            if (array_key_exists($attribute, $searchableAttributes) && $value !== '' && $value !== null) {
                $newQuery = call_user_func($searchableAttributes[$attribute], $query, $value, $attribute);
                if ($newQuery instanceof EloquentBuilder) {
                    return $newQuery;
                }
            }
        }
        return $query;
    }

    /**
     * 搜索字段配置
     * @return array
     */
    protected function searchableAttributes(): array
    {
        if ($this instanceof HasPresetInterface) {
            return $this->getPresetsHelper()->withScene(static::SCENE_LIST)->pickFilter();
        }

        // 表下的所有字段可搜索
        $columns = $this->model()->getConnection()->getSchemaBuilder()->getColumnListing($this->model()->getTable());
        $result = [];
        foreach ($columns as $column) {
            $result[$column] = fn($query, $value, $attribute) => $query->where($attribute, $value);
        }
        return $result;
    }

    /**
     * 构建排序条件
     * @param EloquentBuilder $query
     * @param array $order
     * @return EloquentBuilder
     */
    protected function buildOrder(EloquentBuilder $query, array $order): EloquentBuilder
    {
        if (!$order) {
            $order = $this->defaultOrder ?? [];
        }
        foreach ($order as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query;
    }

    /**
     * 扩展列表的 query
     * @param EloquentBuilder $query
     * @return EloquentBuilder
     */
    protected function extGridQuery(EloquentBuilder $query): EloquentBuilder
    {
        return $query;
    }

    /**
     * 查询分页数据
     * @param EloquentBuilder $query
     * @param int $perPage
     * @param int $page
     * @return PaginatorInterface|DBCollection 返回分页或全量数据
     */
    protected function queryPagination(EloquentBuilder $query, int $perPage, int $page)
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * 处理分页结果
     * @param PaginatorInterface|DBCollection $paginator
     * @return array
     */
    protected function solvePaginationResult($paginator): array
    {
        if ($paginator instanceof LengthAwarePaginator) {
            return [
                'items' => $paginator->items(),
                'total' => $paginator->total(),
            ];
        }
        if ($paginator instanceof Paginator) {
            return [
                'items' => $paginator->items(),
                'hasNext' => $paginator->hasMorePages(),
            ];
        }
        if ($paginator instanceof DBCollection) {
            return [
                'items' => $paginator,
            ];
        }
        throw new \InvalidArgumentException('$paginator type error');
    }

    /**
     * @inheritDoc
     */
    public function detail($id): array
    {
        $query = $this->query();
        $query = $this->extDetailQuery($query);
        $collection = $query->findOrFail($id)
            ->makeHidden($this->hiddenAttributes(static::SCENE_DETAIL))
            ->makeVisible($this->visibleAttributes(static::SCENE_DETAIL))
            ->append($this->appendAttributes(static::SCENE_DETAIL));

        return $this->solveDetailResult($collection);
    }

    /**
     * 扩展 detail 的 query
     * @param EloquentBuilder $query
     * @return EloquentBuilder
     */
    protected function extDetailQuery(EloquentBuilder $query): EloquentBuilder
    {
        return $query;
    }

    /**
     * 处理明细结果
     * @param EloquentModel $query
     * @return array
     */
    protected function solveDetailResult(EloquentModel $query): array
    {
        return $query->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function doCreate(array $data): void
    {
        $model = $this->model();
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
        $this->doSave($model);
    }

    /**
     * @inheritDoc
     */
    protected function doUpdate(array $data, $id): void
    {
        $model = $this->query()->findOrFail($id);
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
        $this->doSave($model);
    }

    /**
     * @param EloquentModel $model
     */
    protected function doSave(EloquentModel $model): void
    {
        $model->save();
    }

    /**
     * @inheritDoc
     */
    public function destroy($id): void
    {
        $model = $this->query()->findOrFail($id);
        $model->delete();
    }

    /**
     * @inheritDoc
     */
    public function recovery($id): void
    {
        /* @phpstan-ignore-next-line */
        $this->query()
            ->withTrashed()
            ->whereKey($id)
            ->restore();
    }
}
