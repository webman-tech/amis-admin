<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Amis\Component;
use Kriss\WebmanAmisAdmin\Exceptions\ActionDisableException;
use Kriss\WebmanAmisAdmin\Repository\RepositoryInterface;
use Webman\Http\Request;
use Webman\Http\Response;

abstract class AmisSourceController
{
    public const SCENE_CREATE = 'create';
    public const SCENE_UPDATE = 'update';

    /**
     * 设置只展示
     * @var bool
     */
    protected bool $onlyShow = false;

    /**
     * @return RepositoryInterface
     */
    abstract protected function repository(): RepositoryInterface;

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
                $request->get(),
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
     * 新增
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        if (!$this->authCreate()) {
            throw new ActionDisableException();
        }
        $this->repository()->create($request->post());
        return amis_response(['result' => 'ok']);
    }

    /**
     * 新增后端是否可用
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
     * 新增前端是否可见
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
     * 详情
     * @param Request $request
     * @param $id
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
     * 详情后端是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authDetail($id = null): bool
    {
        return true;
    }

    /**
     * 详情前端是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authDetailVisible(): string
    {
        return '1==1';
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        if (!$this->authUpdate($id)) {
            throw new ActionDisableException();
        }
        $this->repository()->update($request->post(), $id);
        return amis_response(['result' => 'ok']);
    }

    /**
     * 更新后端是否可用
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
     * 更新前端是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authUpdateVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }

        return '1==1';
    }

    /**
     * 删除
     * @param Request $request
     * @param $id
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
     * 删除后端是否可用
     * @param string|int|null $id
     * @return bool
     */
    protected function authDestroy($id = null): bool
    {
        if ($this->onlyShow) {
            return false;
        }

        return true;
    }

    /**
     * 删除前端是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authDestroyVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }

        return 'this.deleted_at === null';
    }

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
     * 恢复后端是否可用
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
     * 恢复前端是否可见
     * amis 表达式，通过 this 获取当前 model, 如 this.id != 1
     * @return string
     */
    protected function authRecoveryVisible(): string
    {
        if ($this->onlyShow) {
            return '1==0';
        }

        return 'this.deleted_at !== null';
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
        if ($this->authCreate()) {
            $crud->withCreate(
                'post:' . $routePrefix,
                $this->buildFormFields($this->form(static::SCENE_CREATE)),
                $this->authCreateVisible()
            );
        }
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
        if ($this->authDetail()) {
            $actions->withDetail(
                $this->buildDetailAttributes($this->detail()),
                "get:{$routePrefix}/\${id}",
                $this->authDetailVisible()
            );
        }
        if ($this->authUpdate()) {
            $actions->withUpdate(
                $this->buildFormFields($this->form(static::SCENE_UPDATE)),
                "put:{$routePrefix}/\${id}",
                "get:{$routePrefix}/\${id}",
                $this->authUpdateVisible()
            );
        }
        if ($this->authDestroy()) {
            $actions->withDelete(
                "delete:{$routePrefix}/\${id}",
                $this->authDestroyVisible()
            );
        }
        if ($this->authRecovery()) {
            $actions->withRecovery(
                "put:{$routePrefix}/\${id}/recovery",
                $this->authRecoveryVisible()
            );
        }
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

    /**
     * 新增和修改的表单
     * @param string $scene
     * @return array
     */
    protected function form(string $scene): array
    {
        return [
            //Amis\FormField::make()->name('name'),
        ];
    }

    /**
     * @param array $formFields
     * @return array
     */
    protected function buildFormFields(array $formFields): array
    {
        foreach ($formFields as &$item) {
            if (is_string($item)) {
                $item = Amis\FormField::make()->name($item);
            }
            if (is_array($item)) {
                $item = Amis\FormField::make($item);
            }
            if ($item instanceof Component) {
                $item = $item->toArray();
            }
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $formFields;
    }

    /**
     * 明细的字段展示
     * @return array
     */
    protected function detail(): array
    {
        return [
            Amis\DetailAttribute::make()->name('id'),
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
            if ($item instanceof Component) {
                $item = $item->toArray();
            }
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $detailAttributes;
    }
}
