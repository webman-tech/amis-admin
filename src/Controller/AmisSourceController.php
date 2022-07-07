<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Amis\Component;
use Kriss\WebmanAmisAdmin\Exceptions\ActionDisableException;
use Kriss\WebmanAmisAdmin\Repository\RepositoryInterface;
use support\Container;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

abstract class AmisSourceController
{
    public const SCENE_CREATE = 'create';
    public const SCENE_UPDATE = 'update';

    protected Amis $amis;
    protected bool $enableDetail = true;
    protected string $visibleDetail = '1==1';
    protected bool $enableCreate = true;
    protected string $visibleCreate = '1==1';
    protected bool $enableUpdate = true;
    protected string $visibleUpdate = '1==1';
    protected bool $enableDelete = true;
    protected string $visibleDelete = 'this.deleted_at==null';
    protected bool $enableRecovery = true;
    protected string $visibleRecovery = 'this.deleted_at!=null';

    public function __construct()
    {
        $this->amis = Container::get(Amis::class);
    }

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
            try {
                return $this->amis->response($this->repository()->pagination(
                    $request->get('page'),
                    $request->get('perPage'),
                    $request->get(),
                    $order
                ));
            } catch (Throwable $e) {
                return $this->amis->handleException($e, [
                    'class' => get_called_class(),
                    'function' => __FUNCTION__,
                ]);
            }
        }

        return $this->amis->response(
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
        try {
            if (!$this->enableCreate) {
                throw new ActionDisableException();
            }
            $this->repository()->create($request->post());
            return $this->amis->response(['result' => 'ok']);
        } catch (Throwable $e) {
            return $this->amis->handleException($e, [
                'class' => get_called_class(),
                'function' => __FUNCTION__,
            ]);
        }
    }

    /**
     * 明细
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        try {
            if (!$this->enableDetail) {
                throw new ActionDisableException();
            }
            return $this->amis->response($this->repository()->detail($id));
        } catch (Throwable $e) {
            return $this->amis->handleException($e, [
                'class' => get_called_class(),
                'function' => __FUNCTION__,
            ]);
        }
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            if (!$this->enableUpdate) {
                throw new ActionDisableException();
            }
            $this->repository()->update($request->post(), $id);
            return $this->amis->response(['result' => 'ok']);
        } catch (Throwable $e) {
            return $this->amis->handleException($e, [
                'class' => get_called_class(),
                'function' => __FUNCTION__,
            ]);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function destroy(Request $request, $id): Response
    {
        try {
            if (!$this->enableDelete) {
                throw new ActionDisableException();
            }
            $this->repository()->destroy($id);
            return $this->amis->response(['result' => 'ok']);
        } catch (Throwable $e) {
            return $this->amis->handleException($e, [
                'class' => get_called_class(),
                'function' => __FUNCTION__,
            ]);
        }
    }

    /**
     * 恢复
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function recovery(Request $request, $id): Response
    {
        try {
            if (!$this->enableRecovery) {
                throw new ActionDisableException();
            }
            $this->repository()->recovery($id);
            return $this->amis->response(['result' => 'ok']);
        } catch (Throwable $e) {
            return $this->amis->handleException($e, [
                'class' => get_called_class(),
                'function' => __FUNCTION__,
            ]);
        }
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
            ->schema([
                'primaryField' => $this->repository()->getPrimaryKey(),
                'api' => 'get:' . $routePrefix . '?_ajax=1',
                //'quickSaveApi' => 'put:' . $routePrefix . '/all, // 目前没有批量保存接口
                'quickSaveItemApi' => 'put:' . $routePrefix . '/${id}', // 需要 column 配置为 'quickEdit' => ['saveImmediately' => true]
            ])
            ->withColumns(array_merge(
                $this->buildGridColumn($this->grid()),
                [$this->gridActions($routePrefix)],
            ));
        if ($this->enableCreate) {
            $crud->withCreate(
                'post:' . $routePrefix,
                $this->buildFormFields($this->form(static::SCENE_CREATE)),
                $this->visibleCreate
            );
        }
        return $crud;
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
     * @return Amis\GridColumnActions
     */
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        $actions = Amis\GridColumnActions::make();
        if ($this->enableDetail) {
            $actions->withDetail(
                $this->buildDetailAttributes($this->detail()),
                "get:{$routePrefix}/\${id}",
                $this->visibleDetail
            );
        }
        if ($this->enableUpdate) {
            $actions->withUpdate(
                $this->buildFormFields($this->form(static::SCENE_UPDATE)),
                "put:{$routePrefix}/\${id}",
                "get:{$routePrefix}/\${id}",
                $this->visibleUpdate
            );
        }
        if ($this->enableDelete) {
            $actions->withDelete(
                "delete:{$routePrefix}/\${id}",
                $this->visibleDelete
            );
        }
        if ($this->enableRecovery) {
            $actions->withRecovery(
                "put:{$routePrefix}/\${id}/recovery",
                $this->visibleRecovery
            );
        }
        return $actions;
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
