# kriss/webman-amis-admin

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
composer require kriss/webman-amis-admin
```

## 配置

详见： [amis.php](src/config/plugin/kriss/webman-amis-admin/amis.php)

## 使用

TODO

### 多应用支持

1. 复制一份 `config/plugin/kriss/webman-amis-admin/amis.php` 到 `config/plugin/kriss/webman-amis-admin/amis-user.php`

2. 继承 `AmisModuleChangeMiddleware` 实现一个无 `_construct` 的中间件（因为 webman 目前还不支持中间件注册使用 __construct），例如：

```php
<?php

namespace app\middleware;

use Kriss\WebmanAmisAdmin\Middleware\AmisModuleChangeMiddleware;

class AmisModuleChange2User extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-user');
    }
}
```

3. 在响应的路由或全局中间件中引入 `AmisModuleChange2User`

## 扩展

TODO

## 例子

参考使用：[https://github.com/krissss/webman-basic](https://github.com/krissss/webman-basic)