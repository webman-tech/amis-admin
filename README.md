# webman-tech/amis-admin

本项目是从 [webman-tech/components-monorepo](https://github.com/orgs/webman-tech/components-monorepo) 自动 split 出来的，请勿直接修改

## 简介

借用 [amis](https://github.com/baidu/amis) 的 JSON 配置化能力，提供给 webman 快速搭建管理后台的能力。

只做最基础的增删改查封装，具体的业务都不实现。

特性：

- **无依赖**：不依赖第三方组件，Laravel 系和 TP 系都能用
- **无侵入**：不设定任何初始 SQL，业务无关
- **无前端**：基本不需要考虑前端，熟悉 amis 和 PHP 即可
- **高扩展**：amis 的各种组件支持全局控制和页面级控制
- **支持多应用模式**：可以支持类似 admin/agent/user 多后台形式

局限：没有 admin 帐号体系，没有菜单管理，没有权限管理。

## 安装

```bash
composer require webman-tech/amis-admin
```

要求 webman > 1.4，且需关闭 `controller_reuse`（原因：`controller_reuse` 会导致成员变量被缓存，而 `AmisSourceController` 需要使用成员变量做单个请求内的缓存）。

## 核心组件

### AmisSourceController

基础的 CRUD 资源控制器基类，负责控制页面结构和操作按钮权限等。业务控制器继承该类后，通过实现对应方法来定义页面的列表、新增、编辑、删除等行为。

### Repository

`AmisSourceController` 中使用的数据访问层封装，负责提供对数据的增删改查操作。支持多种实现方式，内置 `EloquentRepository` 用于与 Laravel Eloquent ORM 交互。

### Component

amis 组件的封装，目前封装了常用的组件类型和属性。所有 amis 组件都可以通过 `Component::make(['type' => 'xxx'])` 来配置，并支持通过 `schema()` 方法覆盖（支持嵌套覆盖）参数。

### PresetsHelper

用于简化 amis 管理后台字段配置的辅助工具，允许开发者通过预设的方式统一管理字段在不同场景（列表、表单、搜索等）下的展示和验证规则。

## 其他

- 参考使用：[https://github.com/krissss/webman-basic](https://github.com/krissss/webman-basic)
- 不使用 CDN：配合使用 [kriss/composer-assets-plugin](https://github.com/krissss/composer-assets-plugin)

## AI 辅助

- **开发维护**：[AGENTS.md](AGENTS.md) — 面向 AI 的代码结构和开发规范说明
- **使用指南**：[skills/webman-tech-amis-admin-best-practices/SKILL.md](skills/webman-tech-amis-admin-best-practices/SKILL.md) — 面向 AI 的最佳实践，可安装到 Claude Code 的 skills 目录使用
