<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;
use WebmanTech\AmisAdmin\Repository\EloquentRepository;

trait RecoveryTrait
{
    /**
     * 恢复
     * @param Request $request
     * @param string|int $id
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
        if ($this->hiddenDestroy) {
            return false;
        }

        $repository = $this->repository();
        if ($repository instanceof EloquentRepository) {
            // 当模型 use SoftDeleted 后存在
            return method_exists($repository->model(), 'trashed');
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
                "put:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}/recovery",
                $this->authRecoveryVisible()
            );
        }
    }
}
