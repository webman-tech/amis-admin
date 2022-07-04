<?php

return [
    'layout' => [
        /**
         * html 上的 lang 属性
         */
        'lang' => 'zh',
        /**
         * 静态资源
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#sdk
         */
        'assets' => [
            'css' => [
                'https://unpkg.com/amis/sdk/sdk.css',
                'https://unpkg.com/amis/sdk/helper.css',
                'https://unpkg.com/amis/sdk/iconfont.css',
            ],
            'js' => [
                'https://unpkg.com/amis/sdk/sdk.js',
                'https://unpkg.com/history@4.10.1/umd/history.js',
            ],
        ],
        /**
         * 切换主题
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#%E5%88%87%E6%8D%A2%E4%B8%BB%E9%A2%98
         */
        'theme' => '',
        /**
         * 视图文件
         * @see \support\view\Raw::render()
         */
        'view' => 'amis-app',
        'view_path' => '../vendor/kriss/webman-amis-admin/src',
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
         */
        'amisJSON' => [
            'brandName' => config('app.name', 'App Admin'),
            'logo' => '/favicon.ico',
            'api' => '/admin/pages', // 修改成获取菜单的路由
        ],
    ]
];
