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

    protected string $title;
    protected Amis $amis;
    protected bool $enableDetail = true;
    protected bool $enableCreate = true;
    protected bool $enableUpdate = true;
    protected bool $enableDelete = true;
    protected bool $enableRecovery = true;

    public function __construct()
    {
        $this->amis = Container::get(Amis::class);
    }

    abstract public function repository(): RepositoryInterface;

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
            return $this->amis->response($this->repository()->pagination(
                $request->get('page'),
                $request->get('perPage'),
                $request->get(),
                $order
            ));
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
     * 列表的 columns
     * @return array
     */
    protected function grid(): array
    {
        return [
            ['name' => 'id', 'label' => 'ID'],
        ];
    }

    /**
     * 新增和修改的表单
     * @param string $scene
     * @return array
     */
    protected function form(string $scene): array
    {
        return [
            //['type' => 'input-text', 'name' => 'username', 'label' => '用户名'],
        ];
    }

    /**
     * 明细的字段展示
     * @return array
     */
    protected function detail(): array
    {
        return [
            //['type' => 'input-text', 'name' => 'username', 'label' => '用户名'],
        ];
    }

    /**
     * 过滤搜索的字段
     * @return array
     */
    protected function filter(): array
    {
        return [
            ['type' => 'input-text', 'name' => 'id', 'label' => 'ID'],
        ];
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
                'api' => 'get:' . $routePrefix . '?_ajax=1',
                //'quickSaveApi' => 'put:' . $request->path(), // 目前没有批量保存接口
                'quickSaveItemApi' => 'put:' . $routePrefix . '/${id}', // 需要 column 配置为 'quickEdit' => ['saveImmediately' => true]
            ])
            ->withColumns($this->buildGridColumn($this->grid(), $routePrefix));
        if ($this->enableCreate) {
            $crud->withCreate('post:' . $routePrefix, $this->buildFormAttributes($this->form(static::SCENE_CREATE)));
        }
        return $crud;
    }

    /**
     * @param array $gridColumns
     * @param string $routePrefix
     * @return array
     */
    protected function buildGridColumn(array $gridColumns, string $routePrefix): array
    {
        foreach ($gridColumns as &$item) {
            if ($item instanceof Amis\GridColumnActions) {
                if ($this->enableDetail) {
                    $item->withDetail(
                        $this->buildDetailAttributes($this->detail()),
                        "get:{$routePrefix}/\${id}"
                    );
                }
                if ($this->enableUpdate) {
                    $item->withUpdate(
                        $this->buildFormAttributes($this->form(static::SCENE_UPDATE)),
                        "put:{$routePrefix}/\${id}",
                        "get:{$routePrefix}/\${id}"
                    );
                }
                if ($this->enableDelete) {
                    $item->withDelete("delete:{$routePrefix}/\${id}");
                }
                if ($this->enableRecovery) {
                    $item->withRecovery("put:{$routePrefix}/\${id}/recovery");
                }
                $item = $item->toArray();
                continue;
            }

            if ($item instanceof Component) {
                $item = $item->toArray();
            }
            if (!is_array($item)) {
                $item = ['name' => $item];
            }
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $gridColumns;
    }

    /**
     * @param array $detailAttributes
     * @return array
     */
    protected function buildDetailAttributes(array $detailAttributes): array
    {
        foreach ($detailAttributes as &$item) {
            if ($item instanceof Component) {
                $item = $item->toArray();
            }
            if (!is_array($item)) {
                $item = ['name' => $item];
            }
            $item['type'] = $item['type'] ?? 'static';
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $detailAttributes;
    }

    /**
     * @param array $formAttributes
     * @return array
     */
    protected function buildFormAttributes(array $formAttributes): array
    {
        foreach ($formAttributes as &$item) {
            if ($item instanceof Component) {
                $item = $item->toArray();
            }
            if (!is_array($item)) {
                $item = ['name' => $item];
            }
            $item['type'] = $item['type'] ?? 'input-text';
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
        }
        unset($item);
        return $formAttributes;
    }
}
