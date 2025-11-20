<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;

trait UpdateTrait
{
    use CreateUpdateFormTrait;

    /**
     * 更新
     * @param Request $request
     * @param string|int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        if (!$this->authUpdate($id)) {
            throw new ActionDisableException();
        }
        $this->repository()->update((array)$request->post(), $id);
        return amis_response(['result' => 'ok']);
    }

    /**
     * 【后端】判断更新是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authUpdate($id = null): bool
    {
        if ($this->onlyShow) {
            return false;
        }

        return true;
    }

    /**
     * 【前端】判断更新是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authUpdateVisible(): string
    {
        return '1==1';
    }

    /**
     * 添加更新按钮到 action column
     * @param Amis\GridColumnActions $actions
     * @param string $routePrefix
     * @return void
     */
    protected function addUpdateAction(Amis\GridColumnActions $actions, string $routePrefix): void
    {
        if ($this->authUpdate()) {
            $actions->withUpdate(
                $this->buildFormFields($this->form($this->repository()::SCENE_UPDATE)),
                "put:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                "get:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                $this->authUpdateVisible()
            );
        }
    }
}
