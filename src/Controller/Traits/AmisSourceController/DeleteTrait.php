<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;

trait DeleteTrait
{
    /**
     * 删除
     * @param Request $request
     * @param string|int $id
     * @return Response
     */
    public function destroy(Request $request, $id): Response
    {
        if (!$this->authDestroy($id)) {
            throw new ActionDisableException();
        }
        $this->repository()->destroy($id);
        return amis_response(['result' => 'ok']);
    }

    /**
     * 【后端】判断删除是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authDestroy($id = null): bool
    {
        if ($this->onlyShow) {
            return false;
        }
        if ($this->hiddenDestroy) {
            return false;
        }

        return true;
    }

    /**
     * 【前端】判断删除是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authDestroyVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }
        if ($this->hiddenDestroy) {
            return '1==0';
        }

        return '!this.deleted_at';
    }

    /**
     * 添加删除按钮到 action column
     * @param Amis\GridColumnActions $actions
     * @param string $routePrefix
     * @return void
     */
    protected function addDeleteAction(Amis\GridColumnActions $actions, string $routePrefix): void
    {
        if ($this->authDestroy()) {
            $actions->withDelete(
                "delete:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                $this->authDestroyVisible()
            );
        }
    }
}
