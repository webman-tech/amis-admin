# webman-tech/amis-admin

[amis](https://github.com/baidu/amis) For webman quick use ~

## 简介

借用 amis 的 json 配置化能力，提供给 webman 快速搭建管理后台的能力

只做最基础的增删改查封装，具体的业务都不实现

特性：

- 无依赖：不依赖第三方组件，Laravel 系和 TP 系都能用（目前建议 laravel，tp 的实现未做）
- 无侵入：不设定任何初始 sql，业务无关
- 无前端：基本不需要考虑前端，熟悉 amis 和 php 即可
- 高扩展：amis 的各种组件支持全局控制和页面级控制
- 支持多应用模式：可以支持作用于类似 admin/agent/user 多后台形式

局限：

- 功能简单：没有admin帐号体系，没有菜单管理，没有权限管理

## 安装

```bash
composer require webman-tech/amis-admin
```

要求 webman > 1.4 且关闭了 controller_reuse（原因：controller_reuse 导致成员变量会被缓存，AmisSourceController
需要使用到成员做单个请求的缓存）

## 核心组件

### AmisSourceController

是一个基础的 CRUD
资源控制器基类，负责控制页面结构，操作按钮权限等。详细信息请参考 [Controller 和 Repository 设计理念与使用方式](./docs/controller_repository.md)。

### Repository

Repository 是 AmisSourceController
中使用的数据访问层封装，负责提供对数据的增删改查操作。支持多种实现方式，包括 [EloquentRepository](./docs/controller_repository.md#eloquentrepository-作用)
用于与 Laravel Eloquent ORM 交互。

### Component

Amis 组件的封装，目前仅封装了常用的组件类型和属性，但 amis 的所有组件都可以通过 `Component::make(['type' => 'xxx'])`
来配置。所有组件也都支持 `schema()`
方法来覆盖（支持嵌套覆盖）参数。详细信息请参考 [Amis 组件使用指南](./docs/amis_component.md)。

### PresetsHelper

PresetsHelper 是一个用于简化 AMIS
管理后台字段配置的辅助工具。它允许开发者通过预设的方式统一管理字段在不同场景下的展示和验证规则。详细信息请参考 [PresetsHelper 使用说明](./docs/preset_helper.md)。

## 使用

参考使用：[https://github.com/krissss/webman-basic](https://github.com/krissss/webman-basic)

> 注意: Amis 实际上是前后端分离的框架，即数据接口是数据接口，页面配置（json）是页面配置， 因此不能用常规的 PHP 框架下的 admin
> 框架（如 laravel-admin 等）来思考

### 基本使用流程

1. 创建 Repository 处理数据逻辑
2. 创建 Controller 继承 AmisSourceController
3. 在 Controller 中返回 Amis 页面结构

示例代码：

```php
// UserRepository.php
use WebmanTech\AmisAdmin\Repository\EloquentRepository;

class UserRepository extends EloquentRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}

// UserController.php
use WebmanTech\AmisAdmin\Controller\AmisSourceController;

class UserController extends AmisSourceController
{
    protected function createRepository(): RepositoryInterface
    {
        return new UserRepository();
    }
}
```

## 高级功能

### 多应用支持

支持在同一个项目中创建多个独立的后台应用，例如 admin、agent、user 等。详细信息请参考 [多应用支持](./docs/multi_app.md)。

### 常用配置

提供了一些常见的配置示例，如修改 dialog 大小、全局配置组件等。详细信息请参考 [配置技巧](./docs/tips.md)。

## 其他

- 更多文档见 [docs](./docs)

## 不使用 cdn

配合使用 [kriss/composer-assets-plugin](https://github.com/krissss/composer-assets-plugin)
