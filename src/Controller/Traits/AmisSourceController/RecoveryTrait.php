<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;

trait RecoveryTrait
{
    /**
     * 恢复
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function recovery(Request $request, $id): Response
    {
        if (!$this->authRecovery($id)) {
            throw new ActionDisableException();
        }
        $this->repository()->recovery($id);
        return amis_response(['result' => 'ok']);
    }

    /**
     * 【后端】判断恢复是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authRecovery($id = null): bool
    {
        if ($this->onlyShow) {
            return false;
        }

        return true;
    }

    /**
     * 【前端】判断恢复是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authRecoveryVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }

        return 'this.deleted_at';
    }

    /**
     * 添加恢复按钮到 action column
     * @param Amis\GridColumnActions $actions
     * @param string $routePrefix
     * @return void
     */
    protected function addRecoveryAction(Amis\GridColumnActions $actions, string $routePrefix): void
    {
        if ($this->authRecovery()) {
            $actions->withRecovery(
                "put:{$routePrefix}/\${id}/recovery",
                $this->authRecoveryVisible()
            );
        }
    }
}