# 多应用支持

amis-admin 支持在同一项目中创建多个独立的后台应用，每个应用可以有自己独立的配置、菜单和权限体系。

## 实现原理

多应用支持基于 `AmisModuleChangeMiddleware` 中间件实现。该中间件通过在请求对象上设置模块标识，使系统能够根据不同的模块加载对应的配置。

```php
// AmisModuleChangeMiddleware 核心代码
public function process(Request $request, callable $handler): Response
{
    $request->{ConfigHelper::AMIS_MODULE} = $this->moduleName;
    return $handler($request);
}
```

系统通过 `ConfigHelper::get()` 方法获取配置时，会根据当前请求的模块标识加载对应的配置文件。

## 配置步骤

### 1. 复制配置文件

复制一份 `config/plugin/webman-tech/amis-admin/amis.php` 配置文件，命名为新的应用配置文件，例如：

```
config/plugin/webman-tech/amis-admin/amis.php        # 默认应用配置
config/plugin/webman-tech/amis-admin/amis-user.php   # 用户应用配置
config/plugin/webman-tech/amis-admin/amis-agent.php  # 代理应用配置
```

每个配置文件可以独立设置：
- 界面主题、标题、Logo
- 菜单API接口
- 登录页面配置
- 组件默认参数
- 验证器等

### 2. 创建中间件

继承 `AmisModuleChangeMiddleware` 实现一个无参数构造函数的中间件<del>（因为 webman 目前还不支持中间件注册时传递参数）</del>：

```php
<?php

namespace app\middleware;

use WebmanTech\AmisAdmin\Middleware\AmisModuleChangeMiddleware;

class AmisModuleChange2User extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-user'); // 对应配置文件名中的 'amis-user'
    }
}

class AmisModuleChange2Agent extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-agent'); // 对应配置文件名中的 'amis-agent'
    }
}
```

### 3. 应用中间件

在相应的路由或全局中间件中引入自定义的中间件：

```php
// config/route.php
use Webman\Route;

// 后台管理路由
Route::group('/admin', function () {
    // 默认后台应用路由
})->middleware([\app\middleware\AmisModuleChange2Admin::class]);

// 用户中心路由
Route::group('/user', function () {
    // 用户中心应用路由
})->middleware([\app\middleware\AmisModuleChange2User::class]);

// 代理中心路由
Route::group('/agent', function () {
    // 代理中心应用路由
})->middleware([\app\middleware\AmisModuleChange2Agent::class]);
```

## 配置文件说明

每个应用的配置文件结构相同，但可以有不同的配置值：

```php
// config/plugin/webman-tech/amis-admin/amis-user.php
return [
    'assets' => [
        'lang' => fn() => locale(),
        'css' => [/* 用户中心专用CSS */],
        'js' => [/* 用户中心专用JS */],
        'theme' => 'cxd', // 用户中心使用不同的主题
        'locale' => fn() => str_replace('_', '-', locale()),
        'debug' => false,
    ],
    'app' => [
        'amisJSON' => [
            'brandName' => '用户中心',
            'logo' => '/user-logo.ico',
            'api' => route('user.pages'), // 用户中心菜单API
        ],
        'title' => '用户中心',
    ],
    'page_login' => function() {
        return [
            'login_api' => route('user.login'),
            'success_redirect' => route('user'),
        ];
    ],
    // ... 其他配置
];
```

## 使用场景

多应用支持适用于以下场景：

1. **管理后台分离**：管理员后台、用户中心、代理后台等独立系统
2. **权限隔离**：不同角色使用不同的界面和功能
3. **品牌定制**：为不同客户或部门提供定制化的界面
4. **功能分组**：将复杂系统按功能模块拆分成多个独立应用

## 注意事项

1. 配置文件名必须与中间件中指定的模块名一致（去除'amis-'前缀）
2. 每个应用可以有自己的路由、控制器和数据源
3. 不同应用间的数据隔离需要在业务逻辑中自行实现
4. 中间件的顺序可能影响功能，确保 AmisModuleChangeMiddleware 在其他相关中间件之前执行