<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;

trait CreateTrait
{
    use CreateUpdateFormTrait;

    /**
     * 新增
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        if (!$this->authCreate()) {
            throw new ActionDisableException();
        }
        $this->repository()->create(array_replace_recursive($request->post(), $request->file()));
        return amis_response(['result' => 'ok']);
    }

    /**
     * 【后端】判断新增是否可用
     * @return bool
     */
    protected function authCreate(): bool
    {
        if ($this->onlyShow) {
            return false;
        }

        return true;
    }

    /**
     * 【前端】判断新增是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authCreateVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }

        return '1==1';
    }

    /**
     * 添加新增按钮
     * @param Amis\Crud $crud
     * @param string $routePrefix
     * @return void
     */
    protected function addCreateAction(Amis\Crud $crud, string $routePrefix): void
    {
        if ($this->authCreate()) {
            $crud->withCreate(
                'post:' . $routePrefix,
                $this->buildFormFields($this->form(static::SCENE_CREATE)),
                $this->authCreateVisible()
            );
        }
    }
}