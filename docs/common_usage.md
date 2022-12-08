# 常用配置

<details>
<summary>如何修改 crud 的详情打开的 dialog 的 size</summary>
在对应的 controller 中添加以下配置
<pre>
/**
 * @inheritdoc
 */
protected function gridActionsConfig(): array
{
    return [
        'schema_detail' => [
            'dialog' => [
                'size' => 'lg',
            ],
        ],
    ];
}
</pre>
</details>