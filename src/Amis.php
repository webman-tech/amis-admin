<?php

namespace WebmanTech\AmisAdmin;

use Webman\Http\Request;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;
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
            'view_path' => '../vendor/webman-tech/amis-admin/src', // 相对 app 目录
            'assets' => $this->getAssets(),
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
        /**
         * Fix https://github.com/walkor/webman-framework/commit/a559a642058aa9d5fd9dea9d129dc31b615c56eb
         */
        $app = $data['view_path'];
        unset($data['view_path']);

        return Raw::render($data['view'], $data, $app);
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
            'view_path' => '../vendor/webman-tech/amis-admin/src', // 相对 app 目录
            'assets' => $this->getAssets(),
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
        /**
         * Fix https://github.com/walkor/webman-framework/commit/a559a642058aa9d5fd9dea9d129dc31b615c56eb
         */
        $app = $data['view_path'];
        unset($data['view_path']);

        return Raw::render($data['view'], $data, $app);
    }

    /**
     * 获取请求接口的路劲
     * @param Request $request
     * @return string
     */
    public function getRequestPath(Request $request): string
    {
        if ($requestPathGetter = ConfigHelper::get('request_path_getter')) {
            return $requestPathGetter($request);
        }

        return $request->path();
    }

    private function getAssets(): array
    {
        $assets = ConfigHelper::get('assets', []);

        $assets['js'] = $assets['js'] ?? [];
        if (is_callable($assets['js'])) {
            $assets['js'] = call_user_func($assets['js']);
        }
        $assets['js'] = array_map(function ($item) {
            if (is_string($item)) {
                $item = ['type' => 'js', 'content' => $item];
            }
            if (!is_array($item) && !isset($item['type'], $item['content'])) {
                throw new \InvalidArgumentException('js 配置错误');
            }
            return $item;
        }, $assets['js']);

        $assets['lang'] = $assets['lang'] ?? 'zh';
        if (is_callable($assets['lang'])) {
            $assets['lang'] = call_user_func($assets['lang']);
        }

        $assets['locale'] = $assets['locale'] ?? 'zh-CN';
        if (is_callable($assets['locale'])) {
            $assets['locale'] = call_user_func($assets['locale']);
        }

        return $assets;
    }
}
