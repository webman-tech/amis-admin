## 项目概述

基于 [amis](https://github.com/baidu/amis) 的管理后台快速搭建组件，通过 JSON 配置化方式为 webman 提供管理后台能力。

**核心特点**：
- **无依赖**：不依赖第三方组件，Laravel 系和 TP 系都能用（目前建议 Laravel，TP 的实现未做）
- **无侵入**：不设定任何初始 SQL，业务无关
- **无前端**：基本不需要考虑前端，熟悉 amis 和 PHP 即可
- **高扩展**：amis 的各种组件支持全局控制和页面级控制
- **多应用模式**：支持 admin/agent/user 等多后台形式

**局限**：
- 功能简单：没有 admin 帐号体系，没有菜单管理，没有权限管理

## 开发命令

测试、静态分析等通用命令与根项目一致，详见根目录 [AGENTS.md](../../AGENTS.md)。

## 目录结构
- `src/`：
  - `Amis.php`：amis 配置生成主入口
  - `Amis/`：amis 各组件封装（Crud/Page/FormField/GridColumn 等）
  - `Controller/`：
    - `AmisSourceController.php`：数据源控制器，处理 CRUD 操作，通过 `Traits/` 拆分各操作
    - `RenderController.php`：页面渲染控制器
  - `Repository/`：
    - `AbsRepository.php`：基础操作抽象，业务 Repository 继承此类
    - `EloquentRepository.php`：Eloquent 实现
  - `Helper/`：
    - `PresetsHelper.php`：预设助手，提供 CRUD 场景预设配置
  - `Validator/`：验证器接口及 Laravel 实现
  - `Middleware/`：中间件
  - `Exceptions/`：异常类
  - `view/`：视图模板（amis-app/amis-page）
  - `helper.php`：全局辅助函数
- `copy/`：配置文件模板（用于 Install.php）
- `docs/`：文档
- `src/Install.php`：Webman 安装脚本

测试文件位于项目根目录的 `tests/Unit/AmisAdmin/`。测试环境配置和 Helper 函数详见根目录 [AGENTS.md](../../AGENTS.md) 的测试相关章节。

## 代码风格

与根项目保持一致，详见根目录 [AGENTS.md](../../AGENTS.md)。

## 注意事项

1. **controller_reuse 必须关闭**：因为 AmisSourceController 需要使用成员变量做单个请求的缓存
2. **只做基础 CRUD**：不实现具体业务逻辑
3. **amis 版本**：关注 amis 组件版本更新
4. **跨框架**：代码设计时考虑 Laravel 和 TP 的兼容性（目前主要支持 Laravel）
