<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use WebmanTech\AmisAdmin\Contracts\RequestInterface;
use WebmanTech\AmisAdmin\Contracts\ValidatorInterface;
use WebmanTech\AmisAdmin\DTO\RequestQueryDTO;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Impl\NullValidator;
use WebmanTech\AmisAdmin\Support\ClosureHelper;

abstract class BaseAction
{
    protected RequestInterface $request;
    /**
     * @var Model|object
     */
    protected $model;
    protected array $config = [
        /**
         * 构造模型查询
         */
        'model_query' => null,
        /**
         * 主键
         */
        'id_key' => 'id',
        /**
         * 过滤
         */
        'filter' => [],
        /**
         * 排序
         */
        'order' => [],
        /**
         * model 的 with
         */
        'with' => [],
        /**
         * 返回的字段
         */
        'fields' => [],
        /**
         * 数据校验
         */
        'validate' => [
            'rules' => [],
            'messages' => [],
            'customAttributes' => [],
        ],
        /**
         * model 赋值
         */
        'assign' => [],
        /**
         * 模型执行的方法
         */
        'model_method' => null,
    ];
    protected ValidatorInterface $validator;

    final public function __construct(RequestInterface $request, $model, array $config = [])
    {
        $this->request = $request;
        if (is_string($model)) {
            $model = new $model();
        }
        $this->model = $model;
        $this->config = array_merge($this->config, $config);

        $this->validator = new NullValidator();
    }

    public function withValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    abstract public function handle();

    protected function buildQuery(): Builder
    {
        $query = ClosureHelper::getValue($this->config['model_query'], $this->model);
        if (!$query instanceof Builder) {
            $query = $this->model->newQuery();
        }
        return $query;
    }

    protected ?RequestQueryDTO $requestQueryDTO = null;

    protected function getRequestQueryDTO(): RequestQueryDTO
    {
        if ($this->requestQueryDTO === null) {
            $this->requestQueryDTO = new RequestQueryDTO($this->request->getAll());
        }

        return $this->requestQueryDTO;
    }

    protected function getPostData(): array
    {
        return $this->request->postAll();
    }

    protected function getRequestPath(string $key, $default = null)
    {
        return $this->request->getPathParams()[$key] ?? $default;
    }

    protected function solveFilter(Builder $query)
    {
        $get = $this->getRequestQueryDTO();
        $config = ClosureHelper::getValue($this->config['filter'], $query, $get, $this->request);

        if (is_array($config) && $config) {
            $data = $get->getFilter();
            foreach ($config as $key => $operate) {
                if (is_numeric($key)) {
                    $key = $operate;
                    $operate = '=';
                }
                if (!isset($data[$key])) {
                    continue;
                }
                $value = $data[$key];
                if ($operate instanceof \Closure) {
                    $operate($query, $value);
                } elseif ($operate === 'bt') {
                    $query->whereBetween($key, explode(',', $value));
                } elseif ($operate === 'in') {
                    $query->whereIn($key, explode(',', $value));
                } elseif ($operate === 'notIn') {
                    $query->whereNotIn($key, explode(',', $value));
                } else {
                    if ($operate === 'like') {
                        $value = str_replace(
                            ['\\', '_', '%'],
                            ['\\\\', '\\_', '\\%'],
                            $value,
                        );
                        $value = "%{$value}%";
                    }
                    $query->where($key, $operate, $value);
                }
            }
        }
    }

    protected function solveOrder(Builder $query)
    {
        $get = $this->getRequestQueryDTO();
        $config = ClosureHelper::getValue($this->config['order'], $query, $get, $this->request);

        if (is_array($config) && $config) {
            $orderBy = $this->getRequestQueryDTO()->getOrderBy();
            if (!$orderBy) {
                return;
            }
            $orderDir = $get->getOrderDir();
            foreach ($config as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                }
                if ($key === $orderBy) {
                    $query->orderBy($key, $orderDir);
                    return;
                }
            }
        }
    }

    protected function solveWith(Builder $query)
    {
        $get = $this->getRequestQueryDTO();
        $config = ClosureHelper::getValue($this->config['with'], $query, $get, $this->request);

        if (is_array($config) && $config) {
            $query->with($config);
        }
    }

    protected function solveFields($items): array
    {
        $config = ClosureHelper::getValue($this->config['fields']);

        if (is_array($config) && $config) {
            $items = array_map(fn($model) => $this->solveFieldsByModel($model, $config), $items);
        }

        return $items instanceof Arrayable ? $items->toArray() : $items;
    }

    protected function solveFieldsByModel($model, array $fields): array
    {
        $formattedFields = [];
        foreach ($fields as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            $formattedFields[$field] = $definition;
        }

        $data = [];
        foreach ($formattedFields as $field => $definition) {
            if (is_string($definition)) {
                $data[$field] = ArrayHelper::get($model, $definition);
            }
            if (is_callable($definition)) {
                $data[$field] = $definition($model);
            }
        }
        return $data;
    }

    protected function solveValidate(array $data): array
    {
        $config = $this->config['validate'];

        if (ClosureHelper::isClosure($config)) {
            return ClosureHelper::call($config, $data, $this->model, $this->request);
        }
        if (is_array($config)) {
            return $this->validator->validate($data, $config['rules'], $config['messages'], $config['customAttributes']);
        }
        if (method_exists($this->model, 'validate')) {
            return $this->model->validate($data);
        }

        return $data;
    }

    protected function solveAssign(Model $model, array $data)
    {
        $config = $this->config['assign'];

        if (ClosureHelper::isClosure($config)) {
            ClosureHelper::call($config, $model, $data);
            return;
        }

        if (is_array($config) && $config) {
            // load 指定的
            foreach ($config as $key => $attribute) {
                if (is_numeric($key)) {
                    $key = $attribute;
                }
                $model->{$data[$key]} = $data[$attribute];
            }
            return;
        }
        // load all
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
    }

    protected function solveSave(Model $model)
    {
        $config = $this->config['save'];

        if (ClosureHelper::isClosure($config)) {
            ClosureHelper::call($config, $model);
            return;
        }

        $model->save();
    }

    protected function solveDelete(Model $model)
    {
        $config = $this->config['delete'];

        if (ClosureHelper::isClosure($config)) {
            ClosureHelper::call($config, $model);
            return;
        }

        $model->delete();
    }

    protected function solveModelMethod(Model $model, ...$params)
    {
        $config = $this->config['model_method'];

        if (ClosureHelper::isClosure($config)) {
            return ClosureHelper::call($config, $model, ...$params);
        }

        return $model->{$config}(...$params);
    }

    protected function solveRecovery(Model $model)
    {
        $config = $this->config['recovery'];

        if (ClosureHelper::isClosure($config)) {
            ClosureHelper::call($config, $model);
            return;
        }

        $model->restore();
    }

    protected function findById(Builder $query, string $id)
    {
        return $query->whereKey($id)->first();
    }

    protected function findAllByIds(Builder $query, array $ids)
    {
        return $query->whereKey($ids)->get();
    }
}