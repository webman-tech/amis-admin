---
name: webman-tech-amis-admin-best-practices
description: webman-tech/amis-admin 最佳实践。使用场景：用户搭建 amis 管理后台时，给出明确的推荐写法。
---

# webman-tech/amis-admin 最佳实践

## 核心原则

1. **Controller 只负责协调**：页面结构、权限控制，不写业务逻辑
2. **Repository 管数据**：查询、验证、字段配置都在 Repository
3. **PresetItem 统一管字段**：label/grid/form/detail/rule 一处定义，各场景复用

---

## 必须配置

```php
// config/plugin/webman-tech/amis-admin/app.php
return ['enable' => true];
```

**webman 必须关闭 controller_reuse**，否则 AmisSourceController 的成员变量缓存会跨请求污染：

```php
// config/server.php 或 webman 配置
'controller_reuse' => false,
```

---

## 标准 CRUD 写法

### Controller

```php
class UserController extends AmisSourceController
{
    protected function createRepository(): RepositoryInterface
    {
        return new UserRepository();
    }
}
```

Controller 默认提供 `index/store/update/detail/destroy/recovery` 六个接口，路由注册：

```php
Route::group('/admin/users', function () {
    Route::get('', [UserController::class, 'index']);
    Route::post('', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::get('/{id}', [UserController::class, 'detail']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::put('/{id}/recovery', [UserController::class, 'recovery']);
});
```

### Repository（推荐：Eloquent + Preset）

```php
class UserRepository extends EloquentRepository implements HasPresetInterface
{
    use HasPresetTrait;

    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function createPresetsHelper(): PresetsHelper
    {
        return (new PresetsHelper())
            ->withPresets([
                'id' => new PresetItem(label: 'ID', form: false),
                'name' => new PresetItem(
                    label: '名称',
                    rule: 'required|string|max:50',
                    filter: 'like',
                ),
                'status' => new PresetItem(
                    label: '状态',
                    selectOptions: [1 => '启用', 0 => '禁用'],
                    rule: 'required|in:0,1',
                ),
                'created_at' => new PresetItem(label: '创建时间', form: false, filter: 'datetime-range'),
            ])
            ->withCrudSceneKeys(['id', 'name', 'status', 'created_at']);
    }
}
```

---

## PresetItem 配置说明

常用参数见下方，完整参数详见 [references/preset-helper.md](references/preset-helper.md)。

```php
new PresetItem(
    label: '字段名',
    filter: 'like',        // 搜索方式：'='/'like'/'datetime-range'/false/Closure
    grid: true,            // 列表展示：true 自动生成，false 不显示，Closure 自定义
    form: true,            // 表单展示
    detail: true,          // 详情展示
    gridExt: fn(GridColumn $col) => $col->width(100)->sortable(),
    formExt: fn(FormField $field) => $field->placeholder('请输入'),
    formExtDynamic: fn(FormField $field, string $scene) => $field->required($scene === 'create'),
    rule: 'required|string|max:100',
    ruleExtDynamic: fn(?array $rules, string $scene) => $scene === 'create'
        ? array_merge(['required'], $rules ?? []) : $rules,
    selectOptions: [1 => '启用', 0 => '禁用'],
    formDefaultValue: '0',
)
```

Amis 组件（FormField/GridColumn/DetailAttribute 等）的用法详见 [references/amis-components.md](references/amis-components.md)。

---

## 权限控制

在 Controller 中重写 `auth*` 方法：

```php
class UserController extends AmisSourceController
{
    protected bool $onlyShow = false;  // true 时隐藏所有写操作

    // 后端控制是否可操作
    protected function authCreate(): bool
    {
        return Auth::guard()->getUser()?->can('user.create') ?? false;
    }

    // 前端控制按钮是否可见（amis 表达式，this 指向当前行数据）
    protected function authUpdateVisible(): string
    {
        return 'this.id != 1';
    }

    protected function authDelete($id = null): bool
    {
        return $id != 1;
    }
}
```

---

## 自定义列表查询

```php
class UserRepository extends EloquentRepository
{
    // 追加关联查询或固定条件
    protected function extGridQuery(EloquentBuilder $query): EloquentBuilder
    {
        return $query->with(['roles'])->where('deleted', 0);
    }

    // 自定义搜索（不用 Preset 时）
    protected function searchableAttributes(): array
    {
        return [
            'name'    => fn($q, $v) => $q->where('name', 'like', "%{$v}%"),
            'role_id' => fn($q, $v) => $q->whereHas('roles', fn($q) => $q->where('id', $v)),
        ];
    }
}
```

---

## 自定义操作按钮

详细示例见 [references/dialog-patterns.md](references/dialog-patterns.md)。

### Ajax 按钮

```php
->withButtonAjax(
    Amis\GridColumnActions::INDEX_UPDATE + 1,
    '操作名称',
    "post:{$routePrefix}/\${id}/action",
    ['confirmText' => '确定执行？', 'level' => 'danger'],
)
```

### Dialog 模式一：只读展示（检查/诊断）

GET 接口返回 amis JSON，服务端根据实际情况动态决定内容（alert/table 等）：

```php
->withButtonDialog(
    Amis\GridColumnActions::INDEX_UPDATE + 2,
    '检查状态',
    ['type' => 'service', 'schemaApi' => "get:{$routePrefix}/\${id}/check"],
    ['dialog' => ['size' => 'md']],
)
```

### Dialog 模式二：可编辑表单（GET 返回表单+预填，POST 保存）

同一接口处理 GET（返回表单结构+预填数据）和 POST（保存），适合"子配置"类操作：

```php
->withButtonDialog(
    Amis\GridColumnActions::INDEX_UPDATE + 3,
    '子配置',
    ['type' => 'service', 'schemaApi' => "get:{$routePrefix}/\${id}/sub-config"],
    [
        'api' => ['url' => "post:{$routePrefix}/\${id}/sub-config", 'data' => ['field_a' => '${field_a}']],
        'dialog' => ['size' => 'lg', 'body' => ['mode' => 'normal']],
    ],
)
```

多个按钮复用同一套 dialog 结构时，用闭包封装（详见 [references/dialog-patterns.md](references/dialog-patterns.md)）。

### 按钮条件显示（visibleOn）

```php
->withButtonAjax(
    Amis\GridColumnActions::INDEX_UPDATE + 4,
    '触发操作',
    "post:{$routePrefix}/\${id}/trigger",
    ['visibleOn' => '${has_feature}'],  // 引用行数据字段
)
```

`visibleOn` 支持 amis 表达式，`${field}` 引用行数据，`this.id != 1` 写 JS 表达式。

---

## 顶部工具栏按钮

在列表顶部添加全局操作按钮（非行级操）：

```php
protected function amisCrud(Request $request): Amis\Crud
{
    return parent::amisCrud($request)
        ->withHeaderToolbar(
            Amis\Crud::INDEX_CREATE + 1,
            [
                'type' => 'button',
                'label' => '全局操作',
                'level' => 'danger',
                'actionType' => 'ajax',
                'api' => route('admin.xxx.global-action'),
                'confirmText' => '确定执行？',
            ]
        );
}
```

---

## 独立自定义页面

不继承 `AmisSourceController`，直接用 `Page` 构建复杂页面（如多步骤向导）。
详见 [references/custom-page.md](references/custom-page.md)。

---

## Dialog 大小配置

```php
class UserController extends AmisSourceController
{
    // 统一设置新增/修改/详情 dialog 大小
    protected ?array $defaultDialogConfig = ['size' => 'lg'];

    // 或单独控制
    protected function gridActionsConfig(): array
    {
        return [
            'schema_detail' => ['dialog' => ['size' => 'xl']],
            'schema_update' => ['dialog' => ['size' => 'lg']],
        ];
    }
}
```

---

## 多应用（admin/user/agent 等独立后台）

```php
// 1. 复制配置文件
// config/plugin/webman-tech/amis-admin/amis.php       ← 默认 admin
// config/plugin/webman-tech/amis-admin/amis-user.php  ← 用户中心

// 2. 创建中间件（每个应用一个）
class AmisModuleChangeToUser extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-user');
    }
}

// 3. 路由中挂载
Route::group('/user', function () {
    // 用户中心路由...
})->middleware([AmisModuleChangeToUser::class]);
```

---

## 常见错误

| 错误 | 原因 | 解决 |
|------|------|------|
| 列表数据跨请求混乱 | `controller_reuse` 未关闭 | 设置 `'controller_reuse' => false` |
| 表单字段不显示 | PresetItem `form: false` 或未加入 sceneKeys | 检查 `withCrudSceneKeys` 配置 |
| 搜索不生效 | `filter: false` 或未配置 `searchableAttributes` | 给 PresetItem 设置 `filter` 参数 |
| 更新时必填报错 | update 场景缺少 `sometimes` 规则 | PresetItem 会自动添加，无需手动处理 |
| 多应用配置互相干扰 | 未挂载 `AmisModuleChangeMiddleware` | 每个应用路由组挂载对应中间件 |
