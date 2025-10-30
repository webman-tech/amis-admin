# PresetsHelper 使用说明

PresetsHelper 是一个用于简化 AMIS 管理后台字段配置的辅助工具。它允许开发者通过预设的方式统一管理字段在不同场景下的展示和验证规则。

## 核心概念

### PresetItem

[PresetItem](../src/Helper/DTO/PresetItem.php) 是字段预设的定义类，包含字段在各种场景下的配置信息：

- label: 字段标签
- labelRemark: 字段标签备注
- description: 字段描述
- filter: 列表筛选器配置
- grid: 列表展示配置
- form: 表单展示配置
- detail: 详情展示配置
- rule: 验证规则
- ruleMessages: 验证规则错误消息
- ruleCustomAttribute: 验证规则自定义属性名
- selectOptions: 选项配置
- formDefaultValue: 表单默认值

### PresetsHelper

[PresetsHelper](../src/Helper/PresetsHelper.php) 是管理 PresetItem 集合的工具类，提供了一系列方法来提取不同场景下的字段配置。

## 基本使用

### 1. 定义预设

```php
$presetsHelper = new PresetsHelper();
$presetsHelper->withPresets([
    'id' => new PresetItem(
        label: 'ID',
        rule: 'required'
    ),
    'name' => new PresetItem(
        label: '名称',
        rule: 'required|string|max:255'
    ),
    'status' => new PresetItem(
        label: '状态',
        selectOptions: [
            1 => '启用',
            0 => '禁用'
        ]
    )
]);
```

### 2. 设置场景字段

```php
// 设置默认场景字段
$presetsHelper->withDefaultSceneKeys(['id', 'name']);

// 设置 CRUD 场景字段
$presetsHelper->withCrudSceneKeys(['id', 'name', 'status']);

// 自定义场景字段
$presetsHelper->withSceneKeys([
    'custom_scene' => ['id', 'name']
]);
```

### 3. 提取配置

```php
// 提取标签
$labels = $presetsHelper->pickLabel();

// 提取列表字段
$gridColumns = $presetsHelper->pickGrid();

// 提取表单字段
$formFields = $presetsHelper->pickForm();

// 提取详情字段
$detailAttributes = $presetsHelper->pickDetail();

// 提取验证规则
$rules = $presetsHelper->pickRules();
```

## 场景支持

PresetsHelper 支持多种场景：

- `default`: 默认场景
- `list`: 列表场景
- `create`: 创建场景
- `update`: 更新场景
- `detail`: 详情场景

可以通过 [withScene()](../src/Helper/PresetsHelper.php#L100-L107) 方法切换当前场景：

```php
// 切换到创建场景
$presetsHelper->withScene('create');
$formFields = $presetsHelper->pickForm();

// 切换到详情场景
$presetsHelper->withScene('detail');
$detailAttributes = $presetsHelper->pickDetail();
```

## 高级功能

### 1. 动态配置

PresetItem 支持通过闭包实现动态配置：

```php
new PresetItem(
    label: fn() => trans('ID'),
    grid: fn(string $key) => GridColumn::make()->name($key)->sortable(),
    form: fn(string $key) => FormField::make()->name($key)->required(),
    rule: fn() => app()->isLocal() ? 'nullable' : 'required'
)
```

### 2. 扩展配置

可以通过扩展方法对生成的组件进行进一步定制：

```php
new PresetItem(
    gridExt: fn(GridColumn $column) => $column->width(100),
    formExt: fn(FormField $field) => $field->placeholder('请输入'),
    detailExt: fn(DetailAttribute $attribute) => $attribute->copyable()
)
```

### 3. 场景感知扩展

通过动态扩展方法，可以根据不同场景应用不同配置：

```php
new PresetItem(
    formExtDynamic: fn(FormField $field, string $scene) => $field->required($scene === 'create'),
    ruleExtDynamic: fn(array $rules, string $scene) => $scene === 'create' ? array_merge(['required'], $rules) : $rules
)
```

## 在 Repository 中使用

PresetsHelper 通常与 Repository 配合使用，实现字段配置的统一管理：

```php
class UserRepository extends AbsRepository implements HasPresetInterface
{
    use HasPresetTrait;

    public function getPresetsHelper(): PresetsHelper
    {
        return $this->getPresetsHelper()
            ->withPresets([
                'id' => new PresetItem(label: 'ID'),
                'username' => new PresetItem(label: '用户名', rule: 'required'),
                'email' => new PresetItem(label: '邮箱', rule: 'required|email'),
            ])
            ->withCrudSceneKeys(['id', 'username', 'email']);
    }
}
```

在控制器中使用：

```php
// 获取列表字段
$gridColumns = $this->repository()->getPresetsHelper()
    ->withScene(RepositoryInterface::SCENE_LIST)
    ->pickGrid();

// 获取表单字段
$formFields = $this->repository()->getPresetsHelper()
    ->withScene(RepositoryInterface::SCENE_CREATE)
    ->pickForm();

// 获取验证规则
$rules = $this->repository()->getPresetsHelper()
    ->withScene(RepositoryInterface::SCENE_CREATE)
    ->pickRules();
```

## 测试示例

参考 [PresetsHelperTest.php](../../../tests/Unit/AmisAdmin/Helper/PresetsHelperTest.php) 文件中的测试用例，可以了解更多使用方式：

1. 基本功能测试：`withPresets`, `withDefaultSceneKeys`, `withCrudSceneKeys`
2. 场景切换测试：`withScene`, `withSceneKeys`
3. 各种配置项测试：`label`, `filter`, `grid`, `form`, `detail`, `rule` 等
4. 高级功能测试：动态配置、扩展配置、场景感知扩展

通过这些测试用例，可以更好地理解 PresetsHelper 的工作原理和使用方法。