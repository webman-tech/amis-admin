# Amis 组件速查

所有组件均支持链式调用，通过 `make()` 静态方法创建实例，`toArray()` 转为数组。

## FormField — 表单字段

```php
use WebmanTech\AmisAdmin\Amis\FormField;

FormField::make()
    ->name('username')
    ->label('用户名')
    ->placeholder('请输入')
    ->required()
    ->description('至少3个字符')
    ->labelRemark('用于登录')
    ->value('默认值')
    ->visibleOn('this.type == 1')   // amis 表达式，条件显示
    ->disabledOn('this.id != null') // 编辑时禁用

// 常用类型方法
->typeInputPassword()
->typeSelect(['options' => [['label' => '启用', 'value' => 1]]])
->typeInputDate()
->typeInputDateTime()
->typeInputDatetimeRange()
->typeInputImage(['receiver' => '/upload'])
->typeInputFile(['receiver' => '/upload'])
->typeEditor()          // 富文本
->typeInputNumber()
->typeCheckbox()
->typeCheckboxs()
->typeRadio()
->typeHidden()
->typeInputKv()         // key-value 输入
```

## GridColumn — 列表列

```php
use WebmanTech\AmisAdmin\Amis\GridColumn;

GridColumn::make()
    ->name('status')
    ->label('状态')
    ->sortable()
    ->searchable()          // 开启搜索框
    ->quickEdit()           // 行内快速编辑
    ->width(100)
    ->fixed('left')         // 固定列
    ->toggled(false)        // 默认隐藏
    ->truncate(50)          // 截断显示

// 常用类型方法
->typeMapping(['map' => [1 => '启用', 0 => '禁用']])
->typeDatetime()
->typeImage()
->typeTag()
->typeLink()
->typeJson()
->typeTpl('${name}（${id}）')  // 模板渲染
```

## DetailAttribute — 详情字段

```php
use WebmanTech\AmisAdmin\Amis\DetailAttribute;

DetailAttribute::make()
    ->name('token')
    ->label('Token')
    ->copyable()

->typeMapping(['map' => [...]])
->typeDatetime()
->typeImage()
->typeLink()
```

## GridColumnActions — 操作列

```php
use WebmanTech\AmisAdmin\Amis\GridColumnActions;

GridColumnActions::make()
    ->withButtonAjax(50, '重置密码', 'post:/admin/users/${id}/reset')
    ->withButtonDialog(
        60, '分配角色',
        ['type' => 'service', 'schemaApi' => 'get:/admin/users/${id}/roles'],  // dialog body
        ['api' => 'put:/admin/users/${id}/roles'],  // 提交配置
    )
    ->withButtonLink(70, '查看日志', '/admin/logs?user_id=${id}')
    ->withButtonDownload(80, '导出', 'get:/admin/users/${id}/export')
    ->withDivider(45)  // 分割线
```

## Page — 页面容器

```php
use WebmanTech\AmisAdmin\Amis\Page;

// withBody 的 index 控制顺序，数字越小越靠前
Page::make()
    ->withBody(10, $someComponent)
    ->withBody(50, $crud)
    ->toArray()
```

## 全局组件配置

```php
// config/plugin/webman-tech/amis-admin/amis.php
'components' => [
    // 所有图片默认可放大
    'typeImage' => [
        'schema' => ['enlargeAble' => true, 'thumbMode' => 'cover'],
    ],
    // 所有表单字段默认不可清除
    \WebmanTech\AmisAdmin\Amis\FormField::class => [
        'schema' => ['clearable' => false],
    ],
    // 列表列默认左对齐
    \WebmanTech\AmisAdmin\Amis\GridColumn::class => [
        'schema' => ['align' => 'left'],
    ],
],
```
