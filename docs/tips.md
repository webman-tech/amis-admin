# 常用配置

<details>
<summary>如何修改 crud 的详情打开的 dialog 的 size</summary>

在对应的 controller 中添加以下配置
<pre>
// 修改该参数，对于 新增、修改、明细 都使用 lg 的 dialog
protected ?array $defaultDialogConfig = [
    'size' => 'lg',
];

// 单独控制
protected function gridActionsConfig(): array
{
    return [
        // 单独配置 detail
        'schema_detail' => [
            'dialog' => [
                'size' => 'lg',
            ],
        ],
    ];
}
</pre>
</details>

<details>
<summary>如何全局配置一个 amis 的组件</summary>

在 config 的 amis 中 components 中添加以下配置
<pre>
return [
    // ... 其他配置
    /*
     * 用于全局替换组件的默认参数
     * @see Component::$config
     */
    'components' => [
        // 例如: 将列表页的字段默认左显示
        /*\WebmanTech\AmisAdmin\Amis\GridColumn::class => [
            'schema' => [
                'align' => 'left',
            ],
        ],*/
        // typeXxx，xxx 未 amis 的组件 type，通过 schema 会全局注入到每个 type 组件
        'typeImage' => [
            'schema' => [
                'enlargeAble' => true,
            ],
        ],
    ],
];
</pre>
</details>

<details>
<summary>如何配置一个动态的操作表单</summary>

在 controller 中

<pre>
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        return parent::gridActions($routePrefix)
            ->withButtonDialog(
                Amis\GridColumnActions::INDEX_UPDATE + 1,
                '标签配置',
                [
                    'type' => 'service',
                    'schemaApi' => 'get:' . route('admin.service.tagsConfig.get', ['id' => '${id}']),
                ],
                [
                    'api' => [
                        'url' => route("admin.service.tagsConfig.post", ['id' => '${id}']),
                    ],
                    'level' => 'success',
                    'dialog' => [
                        'size' => 'xl',
                        'body' => [
                            'mode' => 'normal',
                        ],
                    ],
                ]
            );
    }

    public function tagsConfig(Request $request, $id)
    {
        $model = Model::findOrFail($id);

        if ($request->method() === 'GET') {
            return admin_response([
                'type' => 'container',
                'data' => [
                    'abc' => '123',
                    'xyz' => '456,
                ],
                'body' => $this->buildFormFields([
                    FormField::make()->name('abc'),
                    FormField::make()->name('xyz'),
                ]),
            ]);
        }

        $model = new Model();
        $model->abc = $request->post('abc');
        $model->xyz = $request->post('xyz');
        $model->save();

        return admin_response('ok');
    }
</pre>

在 route 中添加 `admin.service.tagsConfig.get` 和 `admin.service.tagsConfig.post` 的路由配置

</details>