<?php

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Controller\RenderController;

/**
 * Amis 静态资源的基础 url
 * 建议生产环境使用指定的版本，否则会存在因版本变更引起的问题
 * 更加建议使用公司自己的 cdn，或者将静态资源下载后放到本地，提交速度
 */
$amisAssetBaseUrl = 'https://unpkg.com/amis@latest/sdk/';

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
            $amisAssetBaseUrl . 'sdk.css',
            $amisAssetBaseUrl . 'helper.css',
            $amisAssetBaseUrl . 'iconfont.css',
        ],
        'js' => [
            $amisAssetBaseUrl . 'sdk.js',
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
     * @see Amis::renderApp()
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
     * @see Amis::renderPage()
     */
    'page' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started
         */
        'amisJSON' => [],
    ],
    /**
     * 登录页面配置
     * @see RenderController::login()
     */
    'page_login' => function() {
        return [
            //'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'login_api' => '/admin/auth/login',
            'success_redirect' => '/admin',
        ];
    },
    /**
     * 用于全局替换组件的默认参数
     * @see Component::$config
     */
    'components' => [
        // 例如: 将列表页的字段默认左显示
        /*\WebmanTech\AmisAdmin\Amis\GridColumn::class => [
            'schema' => [
                'align' => 'left',
            ],
        ],*/
    ],
    /**
     * 默认的验证器
     * 返回一个 \WebmanTech\AmisAdmin\Validator\ValidatorInterface
     */
    'validator' => fn() => new \WebmanTech\AmisAdmin\Validator\NullValidator(),
    //'validator' => fn() => new \WebmanTech\AmisAdmin\Validator\LaravelValidator(\support\Container::get(\Illuminate\Contracts\Validation\Factory::class)),
];
