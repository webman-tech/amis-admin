# PresetItem 完整参数说明

```php
new PresetItem(
    // 显示名称，支持闭包
    label: '字段名',
    // label 旁的问号提示
    labelRemark: '备注说明',
    // 表单字段下方的描述文字
    description: '字段描述',

    // 列表搜索方式
    // true/'=' 精确匹配，'like' 模糊，'datetime-range' 时间范围
    // '=int'/'=bool'/'=float' 带类型转换的精确匹配
    // false/null 不可搜索，Closure 自定义
    filter: 'like',

    // grid/form/detail: true 自动生成，false/null 不显示，Closure 自定义
    grid: true,
    form: true,
    detail: true,

    // 扩展已生成的组件（静态，缓存）
    gridExt: fn(GridColumn $col, int $index) => $col->width(100),
    formExt: fn(FormField $field, int $index) => $field->placeholder('请输入'),
    detailExt: fn(DetailAttribute $attr, int $index) => $attr->copyable(),

    // 扩展已生成的组件（动态，感知场景，不缓存）
    gridExtDynamic: fn(GridColumn $col, string $scene, int $index) => $col,
    formExtDynamic: fn(FormField $field, string $scene, int $index) => $field->required($scene === 'create'),
    detailExtDynamic: fn(DetailAttribute $attr, string $scene, int $index) => $attr,

    // 验证规则，默认 nullable
    rule: 'required|string|max:100',
    // 动态规则（感知场景，不缓存）
    ruleExtDynamic: fn(?array $rules, string $scene) => $scene === 'create'
        ? array_merge(['required'], $rules ?? [])
        : $rules,
    ruleMessages: ['username.required' => '用户名不能为空'],
    ruleCustomAttribute: '用户名',

    // 选项配置，自动生成下拉选项和列表映射
    // map 形式: [value => label]
    selectOptions: [1 => '启用', 0 => '禁用'],
    // 或二维数组形式: [['value' => 1, 'label' => '启用'], ...]

    // 表单默认值
    formDefaultValue: '0',
)
```

## 场景说明

| 场景常量 | 值 | 触发时机 |
|---------|-----|---------|
| `SCENE_LIST` | `list` | 列表查询、grid 展示 |
| `SCENE_CREATE` | `create` | 新增表单、新增验证 |
| `SCENE_UPDATE` | `update` | 修改表单、修改验证（自动加 `sometimes`） |
| `SCENE_DETAIL` | `detail` | 详情展示 |

## withSceneKeys 控制各场景字段

```php
(new PresetsHelper())
    ->withPresets([...])
    // 所有 CRUD 场景使用相同字段
    ->withCrudSceneKeys(['id', 'username', 'status'])

    // 或分场景控制
    ->withSceneKeys([
        RepositoryInterface::SCENE_LIST   => ['id', 'username', 'status', 'created_at'],
        RepositoryInterface::SCENE_CREATE => ['username', 'password', 'status'],
        RepositoryInterface::SCENE_UPDATE => ['username', 'status'],
        RepositoryInterface::SCENE_DETAIL => ['id', 'username', 'status', 'created_at'],
    ])
```
