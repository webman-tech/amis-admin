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

局限：

- 功能简单：没有admin帐号体系，没有菜单管理，没有权限管理

## 安装

```bash
composer require kriss/webman-amis-admin
```

## 配置

详见： [amis.php](src/config/plugin/kriss/webman-amis-admin/amis.php)

## 使用

## 扩展

## 例子
