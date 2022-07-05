<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Repository\RepositoryInterface;
use support\Container;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

abstract class AmisSourceController
{
    protected string $title;
    protected Amis $amis;

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

        return $this->amis->response($this->amisPage($request));
    }

    /**
     * 新增
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        try {
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
     * @return array
     */
    protected function form(bool $isUpdate): array
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

    protected function amisPage(Request $request): array
    {
        $data = [
            'type' => 'page',
            'title' => $this->title,
            'body' => [
                [
                    'type' => 'crud',
                    'name' => 'crud',
                    'api' => 'get:' . $request->path() . '?_ajax=1',
                    //'quickSaveApi' => 'put:' . $request->path(), // 目前没有批量保存接口
                    'quickSaveItemApi' => 'put:' . $request->path() . '/${id}', // 需要 column 配置为 'quickEdit' => ['saveImmediately' => true]
                    'syncLocation' => false,
                    'autoGenerateFilter' => true,
                    'headerToolbar' => [
                        'reload',
                        [
                            'type' => 'columns-toggler',
                            'align' => 'right',
                            'draggable' => true,
                            'icon' => 'fas fa-cog',
                        ],
                        [
                            'type' => 'button',
                            'label' => '新增',
                            'icon' => 'fa fa-plus',
                            'actionType' => 'dialog',
                            'className' => 'p-r',
                            'level' => 'primary',
                            'align' => 'right',
                            'dialog' => [
                                'title' => '新增',
                                'body' => [
                                    'type' => 'form',
                                    'api' => 'post:' . $request->path(),
                                    'body' => $this->form(false),
                                ],
                            ],
                        ],
                    ],
                    'footerToolbar' => [
                        'switch-per-page',
                        'pagination',
                    ],
                    'columns' => $this->grid(),
                ],
            ]
        ];
        return $data;
    }
}