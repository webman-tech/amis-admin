<?php

namespace Kriss\WebmanAmisAdmin;

use Kriss\WebmanAmisAdmin\Helper\ArrayHelper;
use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use support\view\Raw;
use Throwable;
use Webman\Http\Response;

class Amis
{
    /**
     * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/types/api?page=1#%E6%8E%A5%E5%8F%A3%E8%BF%94%E5%9B%9E%E6%A0%BC%E5%BC%8F-%E9%87%8D%E8%A6%81-
     * @param array $data
     * @param string $msg
     * @param array $extra
     * @return Response
     */
    public function response(array $data, string $msg = '', array $extra = []): Response
    {
        $data = array_merge([
            'status' => $msg ? 1 : 0,
            'msg' => $msg,
            'data' => $data ?: '{}',
        ], $extra);

        return json($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 渲染 app 多页面结构
     * @param array $schema
     * @return string
     * @throws Throwable
     */
    public function renderApp(array $schema = [])
    {
        $defaultData = [
            'view' => 'amis-app',
            'view_path' => '../vendor/kriss/webman-amis-admin/src', // 相对 app 目录
            'assets' => ConfigHelper::get('assets', []),
        ];
        $appData = ConfigHelper::get('app', []);
        if (isset($appData['amisJSON']) && is_callable($appData['amisJSON'])) {
            $appData['amisJSON'] = call_user_func($appData['amisJSON']);
        }
        $schema['type'] = 'app';
        $data = ArrayHelper::merge(
            $defaultData,
            $appData,
            [
                'amisJSON' => $schema,
            ],
        );
        return Raw::render($data['view'], $data, $data['view_path']);
    }

    /**
     * 渲染单页面
     * @param string $title
     * @param array $schema
     * @return string
     * @throws Throwable
     */
    public function renderPage(string $title, array $schema = [])
    {
        $defaultData = [
            'view' => 'amis-page',
            'view_path' => '../vendor/kriss/webman-amis-admin/src', // 相对 app 目录
            'assets' => ConfigHelper::get('assets', []),
        ];
        $pageData = ConfigHelper::get('page', []);
        if (isset($appData['amisJSON']) && is_callable($appData['amisJSON'])) {
            $pageData['amisJSON'] = call_user_func($appData['amisJSON']);
        }
        $schema['type'] = 'page';
        $data = ArrayHelper::merge(
            $defaultData,
            $pageData,
            [
                'amisJSON' => $schema,
                'title' => $title,
            ],
        );

        return Raw::render($data['view'], $data, $data['view_path']);
    }
}
