# Controller 和 Repository 设计理念与使用方式

amis-admin 采用 Controller-Repository 架构模式，将业务逻辑与数据访问逻辑分离，提高代码的可维护性和可测试性。

## 设计理念

### Controller 职责

[AmisSourceController](../src/Controller/AmisSourceController.php) 是所有业务控制器的基类，负责：

1. **HTTP 请求处理**：接收和解析 HTTP 请求参数
2. **页面结构构建**：构建 AMIS 页面和 CRUD 组件结构
3. **权限控制**：控制操作的可见性和可用性
4. **数据流向控制**：协调 Repository 和视图之间的数据流动

### Repository 职责

[RepositoryInterface](../src/Repository/RepositoryInterface.php) 和 [AbsRepository](../src/Repository/AbsRepository.php)
定义了数据访问层的标准，负责：

1. **数据操作**：增删改查等数据操作
2. **数据验证**：字段验证和数据清洗
3. **数据转换**：将数据转换为 AMIS 所需的格式
4. **业务规则封装**：封装与数据相关的业务规则

## Controller 使用方式

### 基本结构

```php
namespace app\controller\admin;

use WebmanTech\AmisAdmin\Controller\AmisSourceController;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

class UserController extends AmisSourceController
{
    /**
     * 创建 Repository 实例
     * @return RepositoryInterface
     */
    protected function createRepository(): RepositoryInterface
    {
        return new UserRepository();
    }
}
```

### 核心方法

#### index()

处理页面展示和数据列表请求：

```php
public function index(Request $request): Response
{
    // AJAX 请求返回数据
    if ($request->get('_ajax')) {
        return amis_response($this->repository()->pagination(
            (int)$request->get('page'),
            (int)$request->get('perPage'),
            array_filter((array)$request->get(), fn($item) => $item !== ''),
            $order
        ));
    }
    
    // 页面请求返回页面结构
    return amis_response(
        $this->amisPage($request)
            ->withBody(50, $this->amisCrud($request))
            ->toArray()
    );
}
```

#### 页面构建方法

```php
// 构建页面组件
protected function amisPage(Request $request): Amis\Page
{
    return Amis\Page::make();
}

// 构建 CRUD 组件
protected function amisCrud(Request $request): Amis\Crud
{
    // 自动构建包含列表、操作按钮的 CRUD 组件
}
```

### 操作控制

Controller 通过多个 Trait 实现不同的操作：

- [CreateTrait](../src/Controller/Traits/AmisSourceController/CreateTrait.php) - 创建操作
- [UpdateTrait](../src/Controller/Traits/AmisSourceController/UpdateTrait.php) - 更新操作
- [DetailTrait](../src/Controller/Traits/AmisSourceController/DetailTrait.php) - 详情操作
- [DeleteTrait](../src/Controller/Traits/AmisSourceController/DeleteTrait.php) - 删除操作
- [RecoveryTrait](../src/Controller/Traits/AmisSourceController/RecoveryTrait.php) - 恢复操作

每个操作都有对应的权限控制方法：

```php
// 控制创建操作是否可用
protected function authCreate(): bool
{
    return true; // 默认允许
}

// 控制创建按钮是否可见
protected function authCreateVisible(): string
{
    return '1==1'; // AMIS 表达式，默认显示
}
```

### 列表配置

```php
// 定义列表字段
protected function grid(): array
{
    return [
        GridColumn::make()->name('id')->label('ID'),
        GridColumn::make()->name('username')->label('用户名'),
    ];
}

// 定义操作列
protected function gridActions(string $routePrefix): GridColumnActions
{
    return parent::gridActions($routePrefix)
        ->withButtonAjax(50, '自定义操作', 'post:'.$routePrefix.'/${id}/custom');
}
```

## Repository 使用方式

### 基本结构

```php
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class UserRepository extends AbsRepository
{
    // 实现抽象方法
    protected function doCreate(array $data): void
    {
        // 创建用户逻辑
    }
    
    protected function doUpdate(array $data, $id): void
    {
        // 更新用户逻辑
    }
    
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array
    {
        // 分页查询逻辑
    }
    
    public function detail($id): array
    {
        // 详情查询逻辑
    }
    
    public function destroy($id): void
    {
        // 删除逻辑
    }
    
    public function recovery($id): void
    {
        // 恢复逻辑
    }
}
```

### EloquentRepository 作用

[EloquentRepository](../src/Repository/EloquentRepository.php) 是 AbsRepository 的一个具体实现，专门用于与 Laravel Eloquent ORM 进行交互。它提供了一套完整的基于 Eloquent Model 的数据操作方法，极大地简化了数据库操作的开发工作。

主要功能包括：

1. **自动模型初始化**：可以直接传入 Eloquent Model 类名或实例进行初始化
2. **默认排序机制**：默认按主键倒序排列数据
3. **分页查询支持**：内置分页查询逻辑，支持搜索和排序
4. **搜索条件构建**：自动根据字段配置构建搜索条件
5. **排序条件构建**：支持自定义排序规则
6. **软删除支持**：内置软删除和恢复功能支持
7. **字段可见性控制**：支持隐藏/显示特定字段和追加计算字段

使用示例：

```php
use WebmanTech\AmisAdmin\Repository\EloquentRepository;
use app\model\User;

class UserRepository extends EloquentRepository
{
    public function __construct()
    {
        // 传入模型类名
        parent::__construct(User::class);
        
        // 或者传入模型实例
        // parent::__construct(new User());
    }
    
    // 可以覆盖默认方法来自定义行为
    
    /**
     * 扩展列表查询
     */
    protected function extGridQuery(EloquentBuilder $query): EloquentBuilder
    {
        // 添加关联或其他查询条件
        return $query->with(['profile', 'roles']);
    }
    
    /**
     * 自定义搜索字段
     */
    protected function searchableAttributes(): array
    {
        return [
            'username' => fn($query, $value) => $query->where('username', 'like', "%{$value}%"),
            'email' => fn($query, $value) => $query->where('email', $value),
        ];
    }
}
```

EloquentRepository 的优势：
- 减少重复代码：大部分常见的数据库操作已经实现
- 约定优于配置：遵循 Laravel Eloquent 的习惯用法
- 易于扩展：提供多个可重写的方法来定制行为
- 性能优化：合理利用 Eloquent 的特性提升查询效率

### 数据验证

Repository 内置了数据验证机制：

```php
// 定义验证规则
protected function rules(string $scene): array
{
    return [
        'username' => 'required|string|min:3|max:20',
        'email' => 'required|email|unique:users,email',
    ];
}

// 自定义错误消息
protected function ruleMessages(string $scene): array
{
    return [
        'username.required' => '用户名不能为空',
        'email.email' => '邮箱格式不正确',
    ];
}

// 自定义字段名称
protected function ruleCustomAttributes(string $scene): array
{
    return [
        'username' => '用户名',
        'email' => '邮箱',
    ];
}
```

### 字段配置

通过 Preset 机制统一管理字段配置：

```php
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;
use WebmanTech\AmisAdmin\Helper\DTO\PresetItem;

class UserRepository extends AbsRepository implements HasPresetInterface
{
    public function getPresetsHelper(): PresetsHelper
    {
        return (new PresetsHelper())
            ->withPresets([
                'id' => new PresetItem(
                    label: '用户ID',
                    grid: fn(string $key) => GridColumn::make()
                        ->name($key)
                        ->sortable()
                ),
                'username' => new PresetItem(
                    label: '用户名',
                    rule: 'required|string|min:3|max:20',
                    grid: true,
                    form: true,
                    detail: true
                ),
                'email' => new PresetItem(
                    label: '邮箱',
                    rule: 'required|email|unique:users,email',
                    grid: true,
                    form: true,
                    detail: true
                ),
                'status' => new PresetItem(
                    label: '状态',
                    selectOptions: [
                        'active' => '激活',
                        'inactive' => '未激活'
                    ],
                    grid: fn(string $key) => GridColumn::make()
                        ->name($key)
                        ->typeMapping(['map' => [
                            'active' => '激活',
                            'inactive' => '未激活'
                        ]]),
                    form: fn(string $key) => FormField::make()
                        ->name($key)
                        ->typeSelect(['options' => [
                            ['label' => '激活', 'value' => 'active'],
                            ['label' => '未激活', 'value' => 'inactive']
                        ]]),
                    detail: true
                )
            ])
            ->withCrudSceneKeys(['id', 'username', 'email', 'status']);
    }
}
```

## 高级用法

### 权限控制

```php
// Controller 中控制操作权限
protected function authCreate(): bool
{
    return user()->can('user.create');
}

protected function authUpdate($id = null): bool
{
    if ($id && user()->id == $id) {
        return true; // 用户可以更新自己
    }
    return user()->can('user.update');
}
```

### 场景化配置

```php
// 在 Repository 中根据场景返回不同配置
protected function rules(string $scene): array
{
    $rules = [
        'username' => 'required|string|min:3|max:20',
    ];
    
    if ($scene === static::SCENE_CREATE) {
        $rules['password'] = 'required|string|min:6';
    }
    
    return $rules;
}
```

## 最佳实践

1. **保持 Controller 精简**：Controller 只负责协调，业务逻辑放在 Repository
2. **充分利用 Preset 机制**：统一管理字段的显示和验证规则
3. **合理使用场景**：根据不同操作场景返回相应的配置
4. **权限控制前置**：在操作执行前进行权限检查
5. **异常处理统一**：使用内置的异常处理机制
6. **验证规则复用**：通过 PresetItem 统一管理字段验证规则

通过这种 Controller-Repository 架构，可以实现业务逻辑与数据访问的解耦，提高代码的可维护性和可扩展性。