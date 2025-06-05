<?php

namespace WebmanTech\AmisAdmin\Controller;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;

class RenderController
{
    public function app(): string
    {
        return amis()->renderApp();
    }

    public function login(): string
    {
        // 默认值，可以被配置参数替换
        $defaultData = [
            // 以下为常用的替换参数
            'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'title' => config('app.name', trans('登录', [], 'amis-admin')),
            'submit_text' => trans('登录', [], 'amis-admin'),
            'success_msg' => trans('登录成功', [], 'amis-admin'),
            'form_width' => 400,
            'login_api' => '/admin/auth/login',
            'form' => [
                Amis\FormField::make()->name('username')->label(trans('用户名', [], 'amis-admin'))->required(),
                Amis\FormField::make()->name('password')->label(trans('密码', [], 'amis-admin'))->typeInputPassword()->required(),
            ],
            'success_redirect' => '/admin',
            // 用于调整整个表单
            'schema' => [],
            'schema_overwrite' => false,
        ];
        $data = ConfigHelper::get('page_login', []);
        if (is_callable($data)) {
            $data = call_user_func($data);
        }
        if (isset($data['form'])) {
            unset($defaultData['form']);
        }
        $data = ArrayHelper::merge($defaultData, $data);

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

        return amis()->renderPage($data['title'], $schema);
    }
}
