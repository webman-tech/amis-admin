# 独立自定义页面

不继承 `AmisSourceController`，直接用 `Page` 构建复杂页面。

## 多步骤向导

通过给根组件设置 `id`，配合 `onEvent/setValue` 在组件间传递状态，驱动步骤流程。

```php
class WizardController
{
    public function index(): Response
    {
        $page = Page::make(['id' => 'page-root', 'data' => ['step' => 0, 'result' => []]])
            ->withBody(1, [
                'type' => 'container',
                'body' => [
                    // 步骤条
                    ['type' => 'steps', 'value' => '${step}', 'steps' => [
                        ['title' => '步骤一'],
                        ['title' => '步骤二'],
                        ['title' => '完成'],
                    ]],

                    // step 0：第一步（如上传/输入）
                    [
                        'visibleOn' => '${step==0}',
                        'type' => 'form',
                        'api' => route('admin.wizard.step1'),
                        'body' => [
                            ['type' => 'input-file', 'name' => 'file', 'accept' => '.xlsx',
                             'receiver' => route('admin.wizard.step1')],
                        ],
                        'onEvent' => [
                            'submitSucc' => ['actions' => [
                                // 提交成功后更新根组件状态，推进到下一步
                                ['actionType' => 'setValue', 'componentId' => 'page-root',
                                 'args' => ['value' => [
                                     'step' => 1,
                                     'result' => '${event.data.result.data.info}',  // 取服务端返回数据
                                 ]]],
                            ]],
                        ],
                    ],

                    // step 1：第二步（如确认）
                    [
                        'visibleOn' => '${step==1}',
                        'type' => 'form',
                        'api' => route('admin.wizard.step2'),
                        'body' => [
                            ['type' => 'json', 'source' => '${result}'],  // 展示上一步的结果
                        ],
                        'onEvent' => [
                            'submitSucc' => ['actions' => [
                                ['actionType' => 'setValue', 'componentId' => 'page-root',
                                 'args' => ['value' => ['step' => 2]]],
                            ]],
                        ],
                    ],

                    // step 2：完成
                    ['visibleOn' => '${step==2}', 'type' => 'alert', 'level' => 'success', 'body' => '操作完成'],
                ],
            ]);

        return amis_response($page);
    }

    public function step1(Request $request): Response
    {
        // 处理第一步，返回数据供第二步使用
        $info = SomeService::process($request->file('file'));
        return amis_response(['info' => $info]);
    }

    public function step2(Request $request): Response
    {
        // 处理第二步
        SomeService::apply($request->post());
        return amis_response('ok');
    }
}
```

## 关键点

- 根组件设置 `id`，子组件通过 `componentId` 引用它
- `onEvent/submitSucc` 监听表单提交成功，`setValue` 更新根组件的 `data`
- 各步骤用 `visibleOn` 控制显示，`${step}` 驱动流程推进
- 服务端接口返回的数据通过 `${event.data.result.data.xxx}` 取值
- 步骤间需要传递的数据存在根组件的 `data` 中
