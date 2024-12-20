<?php

namespace WebmanTech\AmisAdmin\Controller;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Facades\AmisFacade;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

abstract class AmisSourceController
{
    use Traits\AmisSourceController\CreateTrait;
    use Traits\AmisSourceController\UpdateTrait;
    use Traits\AmisSourceController\DetailTrait;
    use Traits\AmisSourceController\DeleteTrait;
    use Traits\AmisSourceController\RecoveryTrait;

    /**
     * 设置只展示
     * @var bool
     */
    protected bool $onlyShow = false;

    /**
     * @var RepositoryInterface|null
     */
    protected ?RepositoryInterface $repository = null;

    /**
     * 获取 repository
     * @return RepositoryInterface
     */
    protected function repository(): RepositoryInterface
    {
        if ($this->repository === null) {
            $this->repository = $this->createRepository();
        }
        return $this->repository;
    }

    /**
     * 创建 Repository
     * @return RepositoryInterface
     */
    abstract protected function createRepository(): RepositoryInterface;

    /**
     * page 数据和列表数据
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->get('_ajax')) {
            $order = [];
            if ($orderBy = $request->get('orderBy')) {
                $order[$orderBy] = $request->get('orderDir', 'asc');
            }
            return amis_response($this->repository()->pagination(
                $request->get('page'),
                $request->get('perPage'),
                array_filter($request->get(), fn($item) => $item !== ''), // 仅过滤空字符串的，保留为 0 的情况
                $order
            ));
        }

        return amis_response(
            $this->amisPage($request)
                ->withBody(50, $this->amisCrud($request))
                ->toArray()
        );
    }

    /**
     * @param Request $request
     * @return Amis\Page
     */
    protected function amisPage(Request $request): Amis\Page
    {
        return AmisFacade::typePage();
    }

    /**
     * @param Request $request
     * @return Amis\Crud
     */
    protected function amisCrud(Request $request): Amis\Crud
    {
        $routePrefix = AmisFacade::getRequestPath($request);

        $crud = AmisFacade::typeCustomCrud()
            ->config($this->crudConfig())
            ->schema([
                'primaryField' => $this->repository()->getPrimaryKey(),
                'api' => "get:{$routePrefix}?_ajax=1",
                // 批量保存接口，目前不支持
                //'quickSaveApi' => "put:{$routePrefix}/all",
                // 单个快速编辑接口，需要 column 配置为 'quickEdit' => ['saveImmediately' => true]
                // 但是此接口形式会将所有字段都提交更新
                //'quickSaveItemApi' => "put:{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                // 自定义的参数，用于列快速编辑时的 api 情况，可以处理快速编辑仅编辑某个字段
                '_columnQuickEditApi' => [
                    'method' => 'put',
                    'url' => "{$routePrefix}/\${{$this->repository()->getPrimaryKey()}}",
                ],
                'bulkActions' => $this->gridBatchActions(),
            ])
            ->withColumns(array_merge(
                $this->buildGridColumn($this->grid()),
                [$this->gridActions($routePrefix)],
            ));
        $this->addCreateAction($crud, $routePrefix);
        return $crud;
    }

    /**
     * 配置 Amis\Crud 的 config
     * @return array
     */
    protected function crudConfig(): array
    {
        return [];
    }

    /**
     * 列表的 columns
     * @return array
     */
    protected function grid(): array
    {
        return [
            AmisFacade::typeGridColumn()->name($this->repository()->getPrimaryKey()),
        ];
    }

    /**
     * @param array $gridColumns
     * @return array
     */
    protected function buildGridColumn(array $gridColumns): array
    {
        foreach ($gridColumns as &$item) {
            if ($item instanceof Amis\GridColumnActions) {
                $item = $item->toArray();
                continue;
            }

            if (is_string($item)) {
                $item = AmisFacade::typeGridColumn()->name($item);
            }
            if (is_array($item)) {
                $item = AmisFacade::typeGridColumn($item);
            }
            if ($item instanceof Amis\Component) {
                $item = $item->toArray();
            }
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);

        return $gridColumns;
    }

    /**
     * grid 操作栏
     * @param string $routePrefix
     * @return Amis\GridColumnActions
     */
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        $actions = AmisFacade::typeGridColumnActions()->config($this->gridActionsConfig());

        $this->addDetailAction($actions, $routePrefix);
        $this->addUpdateAction($actions, $routePrefix);
        $this->addDeleteAction($actions, $routePrefix);
        $this->addRecoveryAction($actions, $routePrefix);

        return $actions;
    }

    /**
     * 配置 GridColumnActions 的 config
     * @return array
     */
    protected function gridActionsConfig(): array
    {
        return [];
    }

    /**
     * 批量操作
     * @return Amis\GridBatchActions
     */
    protected function gridBatchActions(): Amis\GridBatchActions
    {
        return AmisFacade::typeGridBatchActions();
    }
}
