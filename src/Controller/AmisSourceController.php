<?php

namespace WebmanTech\AmisAdmin\Controller;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

abstract class AmisSourceController
{
    use Traits\AmisSourceController\CreateTrait;
    use Traits\AmisSourceController\UpdateTrait;
    use Traits\AmisSourceController\DetailTrait;
    use Traits\AmisSourceController\DeleteTrait;
    use Traits\AmisSourceController\RecoveryTrait;

    public const SCENE_CREATE = 'create';
    public const SCENE_UPDATE = 'update';

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
        return Amis\Page::make();
    }

    /**
     * @param Request $request
     * @return Amis\Crud
     */
    protected function amisCrud(Request $request): Amis\Crud
    {
        $routePrefix = $request->path();

        $crud = Amis\Crud::make()
            ->config($this->crudConfig())
            ->schema([
                'primaryField' => $this->repository()->getPrimaryKey(),
                'api' => 'get:' . $routePrefix . '?_ajax=1',
                //'quickSaveApi' => 'put:' . $routePrefix . '/all, // 目前没有批量保存接口
                'quickSaveItemApi' => 'put:' . $routePrefix . '/${id}', // 需要 column 配置为 'quickEdit' => ['saveImmediately' => true]
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
            Amis\GridColumn::make()->name('id'),
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
                $item = Amis\GridColumn::make()->name($item);
            }
            if (is_array($item)) {
                $item = Amis\GridColumn::make($item);
            }
            if ($item instanceof Component) {
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
        $actions = Amis\GridColumnActions::make()->config($this->gridActionsConfig());

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
        return Amis\GridBatchActions::make();
    }
}
