<?php

return [
    /**
     * amis 资源
     */
    'assets' => [
        /**
         * html 上的 lang 属性
         */
        'lang' => config('translation.locale', 'zh'),
        /**
         * 静态资源，建议下载下来放到 public 目录下然后替换链接
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#sdk
         */
        'css' => [
            'https://unpkg.com/amis/sdk/sdk.css',
            'https://unpkg.com/amis/sdk/helper.css',
            'https://unpkg.com/amis/sdk/iconfont.css',
        ],
        'js' => [
            'https://unpkg.com/amis/sdk/sdk.js',
            'https://unpkg.com/history@4.10.1/umd/history.js', // 使用 app 必须
        ],
        /**
         * 切换主题
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#%E5%88%87%E6%8D%A2%E4%B8%BB%E9%A2%98
         */
        'theme' => '',
        /**
         * 语言
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/i18n
         */
        'locale' => str_replace('_', '-', config('translation.locale', 'zh-CN')),
        /**
         * debug
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/debug
         */
        'debug' => false,
    ],
    /**
     * @see \Kriss\WebmanAmisAdmin\Amis::renderApp()
     */
    'app' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
         */
        'amisJSON' => [
            'brandName' => config('app.name', 'App Admin'),
            'logo' => '/favicon.ico',
            'api' => '/admin/pages', // 修改成获取菜单的路由
        ],
        'title' => config('app.name'),
    ],
    /**
     * @see \Kriss\WebmanAmisAdmin\Amis::renderPage()
     */
    'page' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started
         */
        'amisJSON' => [],
    ],
    /**
     * 登录页面配置
     * @see \Kriss\WebmanAmisAdmin\Controller\LayoutController::login()
     */
    'page_login' => [
        //'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
        'login_api' => '/admin/auth/login',
        'success_redirect' => '/admin'
    ],
    /**
     * 用于全局替换组件的默认参数
     * @see \Kriss\WebmanAmisAdmin\Amis\Component::$config
     */
    'components' => [
        // 例如: 将列表页的字段默认左显示
        /*\Kriss\WebmanAmisAdmin\Amis\GridColumn::class => [
            'schema' => [
                'align' => 'left',
            ],
        ],*/
    ],
    /**
     * 默认的验证器
     * 返回一个 \Kriss\WebmanAmisAdmin\Validator\ValidatorInterface
     */
    'validator' => fn() => new \Kriss\WebmanAmisAdmin\Validator\NullValidator(),
    //'validator' => fn() => new \Kriss\WebmanAmisAdmin\Validator\LaravelValidator(\support\Container::get(\Illuminate\Contracts\Validation\Factory::class)),
    /**
     * 异常自定义处理
     * 可以通过 $amis->response() 返回数据，也可以再次抛出异常
     */
    'exception_handler' => null,
    /*'exception_handler' => function (\Kriss\WebmanAmisAdmin\Amis $amis, Throwable $e, array $extraInfo = []) {
        throw $e;
    }*/
];
