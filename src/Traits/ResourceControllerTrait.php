<?php

namespace WebmanTech\AmisAdmin\Traits;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Config;
use WebmanTech\AmisAdmin\Contracts\RepositoryInterface;
use WebmanTech\AmisAdmin\Contracts\ResourceOperateExceptionInterface;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;

trait ResourceControllerTrait
{
    protected bool $readOnly = false;

    // 列表
    public function index()
    {
        return $this->doActionByRepository(__FUNCTION__, [], 'pagination');
    }

    // 详情
    public function show(string $id)
    {
        return $this->doActionByRepository(__FUNCTION__, compact('id'), 'detail');
    }

    // 新增
    public function store()
    {
        return $this->doActionByRepository(__FUNCTION__, [], 'create');
    }

    // 更新
    public function update(string $id)
    {
        return $this->doActionByRepository(__FUNCTION__, compact('id'));
    }

    // 删除
    public function destroy(string $id)
    {
        return $this->doActionByRepository(__FUNCTION__, compact('id'), 'delete');
    }

    // 恢复
    public function recovery(string $id)
    {
        return $this->doActionByRepository(__FUNCTION__, compact('id'));
    }

    // 选项接口
    public function options()
    {
        return $this->doActionByRepository(__FUNCTION__);
    }

    // 初始值接口，用于新增
    public function scaffold()
    {
        return $this->doActionByRepository(__FUNCTION__);
    }

    protected function doActionByRepository(string $name, $params = [], string $repoMethod = null)
    {
        $this->checkAuth($name);

        $repoMethod ??= $name;
        try {
            $result = $this->getRepository()->{$repoMethod}(...$params);
            return $this->getAmis()->json($result);
        } catch (ResourceOperateExceptionInterface $e) {
            return $this->getAmis()->json($e->getData(), $e->getMessage());
        }
    }

    protected function checkAuth(string $action): void
    {
        if ($this->readOnly && $action !== 'index') {
            throw new ActionDisableException($action);
        }
    }

    protected ?RepositoryInterface $repository = null;

    protected function getRepository(): RepositoryInterface
    {
        if ($this->repository === null) {
            $this->repository = $this->createRepository()
                ->withRequest($this->getConfig()->getRequest())
                ->withValidator($this->getConfig()->getValidator());
        }

        return $this->repository;
    }

    abstract protected function getConfig(): Config;

    abstract protected function getAmis(): Amis;

    abstract protected function createRepository(): RepositoryInterface;
}
