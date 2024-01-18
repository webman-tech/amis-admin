<?php

namespace WebmanTech\AmisAdmin;

use WebmanTech\AmisAdmin\Contracts\PageReaderInterface;
use WebmanTech\AmisAdmin\Contracts\ResponseInterface;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Support\ClosureHelper;
use WebmanTech\AmisAdmin\Support\JsonHelper;
use WebmanTech\AmisAdmin\Support\ObjectGetOrMakeTrait;

class Config
{
    use ObjectGetOrMakeTrait;

    private array $config = [
        /**
         * amis 模版的静态资源
         */
        'assets' => [
            /**
             * html 上的 lang 属性
             */
            'lang' => 'zh',
            /**
             * 静态资源，建议下载下来放到 public 目录下然后替换链接
             * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#sdk
             */
            'css' => [
                '{amis}/sdk.css',
                '{amis}/helper.css',
                '{amis}/iconfont.css',
            ],
            'js' => [
                '{amis}/sdk.js',
                '{history}/history.js', // 使用 app 必须
                // 可以添加复杂的 script 脚本
                /*[
                    'type' => 'script',
                    'content' => <<<JS
    window.xxx = xxx;
    JS,
                ]*/
            ],
            'css_js_replace' => [
                '{amis}' => 'https://unpkg.com/amis@latest/sdk',
                '{history}' => 'https://unpkg.com/history@4.10.1/umd',
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
            'locale' => 'zh-CN',
            /**
             * debug
             * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/debug
             */
            'debug' => false,
        ],
        /**
         * app layout 配置
         */
        'layout_app' => [
            /**
             * 视图模版
             */
            'view' => __DIR__ . '/view/amis-app.php',
            /**
             * html title
             */
            'title' => 'app admin',
            /**
             * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
             */
            'amis_json' => [
                'brandName' => 'App Admin',
                'logo' => '/favicon.ico',
                'api' => '/pages',
            ],
        ],
        /**
         * page 页面配置
         */
        'layout_page' => [
            /**
             * 视图模版
             */
            'view' => __DIR__ . '/view/amis-page.php',
        ],
        /**
         * 登录页面配置
         */
        'view_login' => [
            /**
             * page 配置
             */
            'amis_json' => [
                '_file' => __DIR__ . '/view/login.json',
                'data' => [
                    'title' => 'Xxx'
                ],
            ],
        ],
        /**
         * response 的实现
         */
        ResponseInterface::class => null,
        /**
         * PageReader 的实现
         */
        PageReaderInterface::class => null,
        /**
         *
         */
        'amis_json_replace' => [
            // 全局
            '_global' => [
                'http://127.0.0.1:8787/' => '/'
            ],
        ],
    ];

    public function __construct(array $config)
    {
        $this->config = ArrayHelper::merge($this->config, $config);
    }

    private ?array $assets = null;

    public function getAssets(): array
    {
        if ($this->assets === null) {
            $assets = $this->config['assets'];
            $replace = $assets['css_js_replace'];
            unset($assets['css_js_replace']);

            $assets['css'] = ClosureHelper::getValue($assets['css'] ?? []);
            $assets['css'] = array_map(function ($item) use ($replace) {
                return strtr($item, $replace);
            }, $assets['css']);

            $assets['js'] = ClosureHelper::getValue($assets['js'] ?? []);
            $assets['js'] = array_map(function ($item) use ($replace) {
                if (is_string($item)) {
                    $item = ['type' => 'js', 'content' => $item];
                }
                if (!is_array($item) && !isset($item['type'], $item['content'])) {
                    throw new \InvalidArgumentException('js 配置错误');
                }
                if ($item['type'] === 'js') {
                    $item['content'] = strtr($item['content'], $replace);
                }
                return $item;
            }, $assets['js']);

            $this->assets = $assets;
        }

        return $this->assets;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->getOrMakeObject(ResponseInterface::class, $this->config[ResponseInterface::class]);
    }

    public function getPageReader(): PageReaderInterface
    {
        return $this->getOrMakeObject(PageReaderInterface::class, $this->config[PageReaderInterface::class]);
    }

    public function getLayoutApp(): array
    {
        $data = $this->config['layout_app'];
        $data['amis_json'] = $this->solveAmisJSON($data['amis_json'] ?? []);
        $data['amis_json']['type'] = 'app'; // 必须
        $data['assets'] = $this->getAssets(); // 必须

        $view = $data['view'];
        unset($data['view']);

        return [$view, $data];
    }

    public function getLayoutPage(string $title, array $amisJSON = []): array
    {
        $data = $this->config['layout_page'];
        $data['amis_json'] = $this->solveAmisJSON($schema['amis_json'] ?? [], $amisJSON);
        $data['amis_json']['type'] = 'page'; // 必须
        $data['assets'] = $this->getAssets(); // 必须
        if ($title) {
            $data['title'] = $title;
        }

        $view = $data['view'];
        unset($data['view']);

        return [$view, $data];
    }

    public function getLayoutLogin(): array
    {
        $data = $this->config['view_login'];
        $data['amis_json'] = $this->solveAmisJSON($data['amis_json']);

        return $this->getLayoutPage($data['title'] ?? 'Login', $data['amis_json']);
    }

    public function solveAmisJSON($amisJSON, array $merge = [], string $replaceKey = null): array
    {
        if (!$amisJSON) {
            return $merge;
        }
        // 支持 callback
        if ($amisJSON instanceof \Closure) {
            $amisJSON = ClosureHelper::getValue($amisJSON);
        }
        // 支持文件导入
        if (is_string($amisJSON)) {
            $amisJSON = JsonHelper::getFromJsonFile($amisJSON);
        }
        // 数组配置
        if (!is_array($amisJSON)) {
            throw new \InvalidArgumentException('amisJSON type error');
        }
        // 支持文件导入，然后覆盖部分
        if (isset($amisJSON['_file'])) {
            $file = $amisJSON['_file'];
            unset($amisJSON['_file']);
            return $this->solveAmisJSON($file, ArrayHelper::merge($amisJSON, $merge));
        }

        // 替换内容
        $replace = array_merge(
            $this->getOrMakeJsonReplace('_global'),
            $replaceKey ? $this->getOrMakeJsonReplace($replaceKey) : []
        );
        if ($replace) {
            $amisJSON = json_decode(strtr(json_encode($amisJSON), $replace), true);
        }
        // 合并数据
        if ($merge) {
            $amisJSON = ArrayHelper::merge($amisJSON, $merge);
        }

        return $amisJSON;
    }

    private array $amisJsonReplace = [];

    private function getOrMakeJsonReplace(string $key)
    {
        if (!isset($this->amisJsonReplace[$key])) {
            $arr = [];
            foreach ($this->config['amis_json_replace'][$key] ?? [] as $from => $to) {
                $from = trim(json_encode($from), '"');
                $to = trim(json_encode($to), '"');
                $arr[$from] = $to;
            }
            $this->amisJsonReplace[$key] = $arr;
        }
        return $this->amisJsonReplace[$key];
    }
}
