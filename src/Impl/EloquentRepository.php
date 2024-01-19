<?php

namespace WebmanTech\AmisAdmin\Impl;

use Illuminate\Database\Eloquent\Model;
use WebmanTech\AmisAdmin\Contracts\RepositoryInterface;
use WebmanTech\AmisAdmin\Contracts\RequestInterface;
use WebmanTech\AmisAdmin\Contracts\ValidatorInterface;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionCreate;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionDelete;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionDetail;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionPagination;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionRecovery;
use WebmanTech\AmisAdmin\Impl\EloquentActions\ActionUpdate;
use WebmanTech\AmisAdmin\Impl\EloquentActions\BaseAction;

class EloquentRepository implements RepositoryInterface
{
    protected RequestInterface $request;
    protected ValidatorInterface $validator;
    /**
     * @var string|Model
     */
    protected $modelClass;
    protected array $actions = [];

    final public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;

        $this->actions = [
            'pagination' => [
                'action' => ActionPagination::class,
                'config' => [
                ],
            ],
            'detail' => [
                'action' => ActionDetail::class,
            ],
            'update' => [
                'action' => ActionUpdate::class,
            ],
            'create' => [
                'action' => ActionCreate::class,
            ],
            'delete' => [
                'action' => ActionDelete::class,
            ],
            'recovery' => [
                'action' => ActionRecovery::class,
            ],
        ];

        $this->init();
    }

    protected function init()
    {
    }

    public function withRequest(RequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function withValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    public function addAction(string $name, string $action, array $config): self
    {
        $this->actions[$name] = [
            'action' => $action,
            'config' => $config,
        ];
        return $this;
    }

    public function updateAction(string $name, array $config, string $action = null): RepositoryInterface
    {
        $this->actions[$name]['config'] = ArrayHelper::merge($this->actions[$name]['config'] ?? [], $config);
        if ($action) {
            $this->actions[$name]['action'] = $action;
        }
        return $this;
    }

    public function __call($name, $arguments)
    {
        $actionConfig = $this->actions[$name] ?? null;
        if ($actionConfig === null) {
            throw new \InvalidArgumentException('action not exist: ' . $name);
        }
        $this->request->setPathParams($arguments);

        /** @var BaseAction $action */
        $action = new $actionConfig['action']($this->request, $this->modelClass, $actionConfig['config'] ?? []);
        $action->withValidator($this->validator);
        return $action->handle();
    }
}
