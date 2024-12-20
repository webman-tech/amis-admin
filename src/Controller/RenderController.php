<?php

namespace WebmanTech\AmisAdmin\Controller;

use WebmanTech\AmisAdmin\Facades\AmisFacade;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;

class RenderController
{
    public function app()
    {
        return AmisFacade::renderApp();
    }

    public function login()
    {
        // 默认值，可以被配置参数替换
        $defaultData = [
            // 以下为常用的替换参数
            'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'title' => config('app.name', '登录'),
            'submit_text' => '登录',
            'success_msg' => '登录成功',
            'form_width' => 400,
            'login_api' => '/admin/auth/login',
            'form' => [
                AmisFacade::typeInputText()->name('username')->label('用户名')->required(),
                AmisFacade::typeInputPassword()->name('password')->label('密码')->required(),
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
            $schema = AmisFacade::typePage()
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

        return AmisFacade::renderPage($data['title'], $schema);
    }
}
