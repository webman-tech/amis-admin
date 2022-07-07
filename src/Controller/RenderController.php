<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Helper\ArrayHelper;
use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use support\Container;

class RenderController
{
    public function app()
    {
        return Container::get(Amis::class)->renderApp();
    }

    public function login()
    {
        // 默认值，可以被配置参数替换
        $data = [
            // 以下为常用的替换参数
            'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'title' => config('app.name', '登录'),
            'submit_text' => '登录',
            'success_msg' => '登录成功',
            'form_width' => 400,
            'login_api' => '/admin/auth/login',
            'form' => [
                Amis\FormField::make()->name('username')->label('用户名')->required(),
                Amis\FormField::make()->name('password')->label('密码')->typeInputPassword()->required(),
            ],
            'success_redirect' => '/admin',
            // 用于调整整个表单
            'schema' => [],
            'schema_overwrite' => false,
        ];
        $data = ArrayHelper::merge(ConfigHelper::get('amis.page_login', []), $data);

        $schema = [];
        if (!$data['schema_overwrite']) {
            $schema = Amis\Page::make()
                ->schema([
                    'cssVars' => [
                        '--Page-body-padding' => 0,
                    ],
                ])
                ->withBody(0, [
                    'type' => 'flex',
                    'justify' => 'center',
                    'alignItems' => 'center',
                    'style' => [
                        'height' => '100%',
                        'background' => $data['background'],
                    ],
                    'items' => [
                        [
                            'type' => 'container',
                            'style' => [
                                'width' => $data['form_width'],
                            ],
                            'body' => [
                                'type' => 'form',
                                'title' => [
                                    'type' => 'tpl',
                                    'tpl' => $data['title'],
                                    'className' => 'text-lg flex justify-center'
                                ],
                                'submitText' => $data['submit_text'],
                                'api' => $data['login_api'],
                                'body' => $data['form'],
                                'redirect' => $data['success_redirect'],
                                'messages' => [
                                    'saveSuccess' => $data['success_msg'],
                                ]
                            ],
                        ]
                    ],
                ])
                ->toArray();
        }
        $schema = ArrayHelper::merge($schema, $data['schema']);

        return Container::get(Amis::class)->renderPage($data['title'], $schema);
    }
}
