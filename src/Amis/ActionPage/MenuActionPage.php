<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

class MenuActionPage extends BaseActionPage
{
    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        return [
            'pages' => $this->menus(),
        ];
    }

    protected function menus(): array
    {
        // TODO 从路由注册中自动提取？

        return [
            ['label' => 'Dashboard', 'path' => '/dashboard', 'icon' => 'fa fa-tachometer'],
            ['label' => 'Users', 'path' => '/users', 'icon' => 'fa fa-users'],
            ['label' => 'Roles', 'path' => '/roles', 'icon' => 'fa fa-user-secret'],
            ['label' => 'Permissions', 'path' => '/permissions', 'icon' => 'fa fa-key'],
            ['label' => 'Menu', 'path' => '/menu', 'icon' => 'fa fa-list'],
            ['label' => 'Settings', 'path' => '/settings', 'icon' => 'fa fa-cog'],
        ];
    }
}
