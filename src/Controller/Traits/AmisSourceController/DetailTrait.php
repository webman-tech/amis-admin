<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Exceptions\ActionDisableException;
use WebmanTech\AmisAdmin\Repository\AbsRepository;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;

trait DetailTrait
{
    /**
     * 详情
     * @param Request $request
     * @param string|int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        if (!$this->authDetail($id)) {
            throw new ActionDisableException();
        }
        return amis_response($this->repository()->detail($id));
    }

    /**
     * 【后端】详情判断是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authDetail($id = null): bool
    {
        return true;
    }

    /**
     * 【前端】详情判断是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authDetailVisible(): string
    {
        return '1==1';
    }

    /**
     * 添加详情按钮到 action column
     * @param Amis\GridColumnActions $actions
     * @param string $routePrefix
     * @return void
     */
    protected function addDetailAction(Amis\GridColumnActions $actions, string $routePrefix): void
    {
        if ($this->authDetail()) {
            $actions->withDetail(
                $this->buildDetailAttributes($this->detail()),
                "get:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                $this->authDetailVisible()
            );
        }
    }

    /**
     * 明细的字段展示
     * @return array
     */
    protected function detail(): array
    {
        $repository = $this->repository();
        if ($repository instanceof HasPresetInterface) {
            return $repository->getPresetsHelper()->withScene(AbsRepository::SCENE_DETAIL)->pickDetail();
        }

        return [
            Amis\DetailAttribute::make()->name($this->repository()->getPrimaryKey()),
        ];
    }

    /**
     * @param array $detailAttributes
     * @return array
     */
    protected function buildDetailAttributes(array $detailAttributes): array
    {
        foreach ($detailAttributes as &$item) {
            if (is_string($item)) {
                $item = Amis\DetailAttribute::make()->name($item);
            }
            if (is_array($item)) {
                $item = Amis\DetailAttribute::make($item);
            }
            if ($item instanceof Amis\Component) {
                $item = $item->toArray();
            }
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $detailAttributes;
    }
}
