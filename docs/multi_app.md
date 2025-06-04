# 多应用支持

1. 复制一份 `config/plugin/webman-tech/amis-admin/amis.php` 到 `config/plugin/webman-tech/amis-admin/amis-user.php`

2. 继承 `AmisModuleChangeMiddleware` 实现一个无 `__construct` 的中间件（因为 webman 目前还不支持中间件注册使用 __construct），例如：

```php
<?php

namespace app\middleware;

use WebmanTech\AmisAdmin\Middleware\AmisModuleChangeMiddleware;

class AmisModuleChange2User extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-user');
    }
}
```

3. 在相应的路由或全局中间件中引入 `AmisModuleChange2User`