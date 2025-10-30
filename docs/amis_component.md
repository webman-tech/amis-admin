# Amis 组件使用指南

amis-admin 提供了一系列 Amis 组件类，用于构建 AMIS 页面结构。这些组件类封装了 AMIS 的 JSON 配置，使开发者能够以面向对象的方式构建页面。

## 组件基础 Component

[Component](../src/Amis/Component.php) 是所有 Amis 组件的基类，提供了以下核心功能：

1. 统一的配置管理机制
2. 全局组件配置支持
3. 数组转换方法

### 基本用法

```php
// 创建组件实例
$field = FormField::make();

// 设置组件 schema
$field->schema(['name' => 'username', 'label' => '用户名']);

// 获取组件数组表示
$array = $field->toArray();
```

### 全局配置

可以通过配置文件全局配置组件默认参数：

```php
// config/plugin/webman-tech/amis-admin/amis.php
'components' => [
    // 针对特定组件类的配置
    \WebmanTech\AmisAdmin\Amis\FormField::class => [
        'schema' => [
            'clearable' => false,
        ],
    ],
    // 针对特定类型组件的配置
    'typeImage' => [
        'schema' => [
            'enlargeAble' => true,
        ],
    ],
],
```

## 表单字段 FormField

[FormField](../src/Amis/FormField.php) 用于构建表单字段，支持所有 AMIS 表单组件类型。

### 基本用法

```php
// 基本文本输入框
$field = FormField::make()->name('username')->label('用户名');

// 使用特定类型方法
$emailField = FormField::make()->name('email')->label('邮箱')->typeInputEmail();

// 设置默认值
$field = FormField::make()->name('status')->label('状态')->value(1);

// 设置验证规则
$field = FormField::make()->name('username')->label('用户名')->required()->validations(['minLength' => 3]);
```

### 支持的类型方法

FormField 提供了丰富的类型方法：

- `typeInputText()` - 文本输入框
- `typeInputEmail()` - 邮箱输入框
- `typeInputPassword()` - 密码输入框
- `typeSelect()` - 下拉选择
- `typeRadio()` - 单选框
- `typeCheckbox()` - 复选框
- `typeInputDate()` - 日期选择
- `typeInputImage()` - 图片上传
- 更多类型请参考类定义

## 列表字段 GridColumn

[GridColumn](../src/Amis/GridColumn.php) 用于构建表格列。

### 基本用法

```php
// 基本列
$column = GridColumn::make()->name('username')->label('用户名');

// 可排序列
$column = GridColumn::make()->name('created_at')->label('创建时间')->sortable();

// 可搜索列
$column = GridColumn::make()->name('username')->label('用户名')->searchable();

// 快速编辑
$column = GridColumn::make()->name('title')->label('标题')->quickEdit();

// 截断显示
$column = GridColumn::make()->name('content')->label('内容')->truncate(50);

// 图片列
$imageColumn = GridColumn::make()->name('avatar')->label('头像')->typeImage();

// 状态映射列
$statusColumn = GridColumn::make()->name('status')->label('状态')->typeMapping([
    'map' => [
        1 => '启用',
        0 => '禁用'
    ]
]);

// 进度条列
$progressColumn = GridColumn::make()->name('progress')->label('进度')->typeProgress();

// 更多类型请参考类定义
```

## 详情字段 DetailAttribute

[DetailAttribute](../src/Amis/DetailAttribute.php) 用于构建详情页面字段。

### 基本用法

```php
// 基本详情字段
$attribute = DetailAttribute::make()->name('username')->label('用户名');

// 可复制字段
$attribute = DetailAttribute::make()->name('token')->label('Token')->copyable();

// 特定类型
$emailAttribute = DetailAttribute::make()->name('email')->label('邮箱')->typeLink();

// 更多类型请参考类定义
```

## 操作列 GridColumnActions

[GridColumnActions](../src/Amis/GridColumnActions.php) 用于构建表格操作列。

### 基本用法

```php
$actions = GridColumnActions::make();

// 添加详情按钮
$actions->withDetail($detailAttributes, "get:/api/user/${id}");

// 添加编辑按钮
$actions->withUpdate($formFields, "put:/api/user/${id}", "get:/api/user/${id}");

// 添加删除按钮
$actions->withDelete("delete:/api/user/${id}");

// 添加自定义按钮
$actions->withButtonAjax(50, '审核', 'post:/api/user/${id}/approve');

// 更多类型请参考类定义
```

## 表格组件 Crud

[Crud](../src/Amis/Crud.php) 用于构建完整的数据表格页面。

### 基本用法

```php
$crud = Crud::make()->api('/api/users');

// 设置列
$crud->withColumns([
    GridColumn::make()->name('id')->label('ID'),
    GridColumn::make()->name('username')->label('用户名'),
    GridColumnActions::make()->withDetail($detailAttributes),
]);

// 添加创建按钮
$crud->withCreate('post:/api/users', $formFields);
```

## 操作按钮组 ActionButtons

[ActionButtons](../src/Amis/ActionButtons.php) 用于构建一组操作按钮，常用于列表页的操作列和顶部操作栏等需要按钮操作的地方。

### 基本用法

```php
$actionButtons = ActionButtons::make();

// 添加 AJAX 请求按钮
$actionButtons->withButtonAjax(10, '保存', 'post:/api/users');

// 添加下载按钮
$actionButtons->withButtonDownload(20, '导出', 'get:/api/users/export');

// 添加跳转链接按钮
$actionButtons->withButtonLink(30, '返回列表', '/users');

// 添加弹窗按钮
$actionButtons->withButtonDialog(40, '审核', $formFields, ['api' => 'post:/api/users/${id}/approve']);

// 添加抽屉按钮
$actionButtons->withButtonDrawer(50, '详情', $detailAttributes, ['api' => 'get:/api/users/${id}']);

// 添加分割线
$actionButtons->withDivider(60);

// 添加自定义按钮
$actionButtons->withButton(70, '自定义', ['actionType' => 'ajax', 'api' => 'post:/api/custom']);
```

## 页面组件 Page

[Page](../src/Amis/Page.php) 用于构建 AMIS 页面结构，是整个页面的容器。

### 基本用法

```php
$page = Page::make();

// 添加页面主体内容
$page->withBody(10, $crud);
$page->withBody(20, $form);
$page->withBody(30, $detail);

// 也可以直接添加数组配置
$page->withBody(10, [
    'type' => 'tpl',
    'tpl' => 'Hello World'
]);
```

## 组件配置最佳实践

### 1. 全局配置

对于项目中通用的组件样式，建议使用全局配置：

```php
// config/plugin/webman-tech/amis-admin/amis.php
'components' => [
    // 所有图片组件默认可放大
    'typeImage' => [
        'schema' => [
            'enlargeAble' => true,
            'thumbMode' => 'cover',
        ],
    ],
    // 表单字段默认不可清除
    \WebmanTech\AmisAdmin\Amis\FormField::class => [
        'schema' => [
            'clearable' => false,
        ],
    ],
],
```

### 2. 动态配置

对于需要根据条件变化的配置，可以使用闭包：

```php
// 在控制器中动态配置
$columns = [
    GridColumn::make()->name('id')->label('ID'),
];

// 根据权限添加操作列
if ($this->hasPermission('user.edit')) {
    $columns[] = GridColumnActions::make()
        ->withUpdate($formFields, "put:/api/users/${id}")
        ->withDelete("delete:/api/users/${id}");
}
```

### 3. 组件复用

对于常用的组件配置，可以创建专门的方法：

```php
protected function buildStatusColumn(): GridColumn
{
    return GridColumn::make()
        ->name('status')
        ->label('状态')
        ->typeMapping(['map' => [1 => '启用', 0 => '禁用']])
        ->searchable()
        ->quickEdit([
            'type' => 'select',
            'options' => [['label' => '启用', 'value' => 1], ['label' => '禁用', 'value' => 0]]
        ]);
}

protected function buildCreatedAtColumn(): GridColumn
{
    return GridColumn::make()
        ->name('created_at')
        ->label('创建时间')
        ->typeDatetime()
        ->sortable();
}
```

## 常用技巧

### 1. 链式调用

所有组件都支持链式调用：

```php
$field = FormField::make()
    ->name('username')
    ->label('用户名')
    ->placeholder('请输入用户名')
    ->required()
    ->validations(['minLength' => 3])
    ->description('用户名至少3个字符');
```

### 2. 组件嵌套

组件可以嵌套使用：

```php
$page = Page::make()->withBody(0, [
    'type' => 'crud',
    'api' => '/api/users',
    'columns' => [
        GridColumn::make()->name('username')->label('用户名'),
        GridColumnActions::make()->withDetail([
            DetailAttribute::make()->name('username')->label('用户名'),
            DetailAttribute::make()->name('email')->label('邮箱'),
        ]),
    ]
]);
```

### 3. 条件显示

使用 visibleOn 和 hiddenOn 控制组件显示：

```php
// 根据条件显示字段
$field = FormField::make()
    ->name('reason')
    ->label('原因')
    ->visibleOn('this.status == 0'); // 当 status 为 0 时显示
```

通过合理使用这些 Amis 组件，可以快速构建功能丰富、界面美观的管理后台页面。