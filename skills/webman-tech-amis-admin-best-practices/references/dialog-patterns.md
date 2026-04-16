# Dialog 模式详解

## 模式一：只读展示（检查/诊断）

适合"检查配置"、"查看状态"等只读操作，服务端根据实际情况动态返回 amis JSON。

```php
// Controller
->withButtonDialog(
    Amis\GridColumnActions::INDEX_UPDATE + 1,
    '检查状态',
    ['type' => 'service', 'schemaApi' => "get:{$routePrefix}/\${id}/check"],
    ['level' => 'success', 'dialog' => ['size' => 'md']],
)
```

```php
// 服务端：根据实际情况动态决定返回内容
public function check(Request $request, $id): Response
{
    $model = Model::findOrFail($id);

    try {
        SomeService::verify($model);
        $alert = ['type' => 'alert', 'level' => 'success', 'body' => '检查正常'];
    } catch (\Throwable $e) {
        $alert = ['type' => 'alert', 'level' => 'danger', 'body' => '异常：' . $e->getMessage()];
    }

    return amis_response([
        'type' => 'container',
        'body' => [
            $alert,
            [
                'type' => 'table',
                'data' => [
                    ['key' => '字段A', 'value' => $model->field_a],
                    ['key' => '字段B', 'value' => $model->field_b],
                ],
                'columns' => [
                    ['label' => 'Key', 'name' => 'key'],
                    ['label' => '值', 'name' => 'value'],
                ],
            ],
        ],
    ]);
}
```

---

## 模式二：可编辑表单（GET 返回表单+预填，POST 保存）

适合"子配置"类操作，同一接口处理 GET（返回表单结构+预填数据）和 POST（保存）。

```php
// Controller
->withButtonDialog(
    Amis\GridColumnActions::INDEX_UPDATE + 2,
    '子配置',
    ['type' => 'service', 'schemaApi' => "get:{$routePrefix}/\${id}/sub-config"],
    [
        'api' => [
            'url' => "post:{$routePrefix}/\${id}/sub-config",
            'data' => ['field_a' => '${field_a}', 'field_b' => '${field_b}'],
        ],
        'dialog' => ['size' => 'lg', 'body' => ['mode' => 'normal']],
    ],
)
```

```php
// 服务端
public function subConfig(Request $request, $id): Response
{
    $model = Model::findOrFail($id);

    if ($request->method() === 'GET') {
        return amis_response([
            'type' => 'container',
            'data' => [                          // 预填表单数据
                'field_a' => $model->field_a,
                'field_b' => $model->field_b,
                'options'  => RelatedModel::getOptions(),  // 下拉选项等动态数据
            ],
            'body' => $this->buildFormFields(    // 表单字段（可用 scene 区分）
                $this->form(Repository::SCENE_SUB_CONFIG)
            ),
        ]);
    }

    // POST：保存
    $model->field_a = $request->post('field_a');
    $model->field_b = $request->post('field_b');
    $model->save();
    return amis_response('ok');
}
```

---

## 多个按钮复用同一套 dialog 结构

当有多个"子配置"类按钮时，用闭包封装 dialog 结构，避免重复：

```php
protected function gridActions(string $routePrefix): Amis\GridColumnActions
{
    // 封装 schemaApi + api 的生成逻辑
    $dialog = function (string $action, array $postFields = [], array $extra = []) use ($routePrefix) {
        $data = array_combine($postFields, array_map(fn($f) => '${' . $f . '}', $postFields));
        return [
            ['type' => 'service', 'schemaApi' => "get:{$routePrefix}/\${id}/{$action}"],
            array_merge([
                'api' => array_filter(['url' => "post:{$routePrefix}/\${id}/{$action}", 'data' => $data]),
                'dialog' => ['size' => 'lg', 'body' => ['mode' => 'normal']],
            ], $extra),
        ];
    };

    return parent::gridActions($routePrefix)
        ->withButtonDialog(Amis\GridColumnActions::INDEX_UPDATE + 1, '配置A',
            ...$dialog('config-a', ['field_a', 'field_b']))
        ->withButtonDialog(Amis\GridColumnActions::INDEX_UPDATE + 2, '配置B',
            ...$dialog('config-b', ['field_c'], ['visibleOn' => '${can_config_b}']))
        ->withButtonDialog(Amis\GridColumnActions::INDEX_UPDATE + 3, '复制创建',
            ...$dialog('copy-create'));
}
```
