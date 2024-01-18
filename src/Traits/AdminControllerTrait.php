<?php

namespace WebmanTech\AmisAdmin\Traits;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Config;

trait AdminControllerTrait
{
    // admin 页面的视图
    public function layoutApp()
    {
        $amis = $this->getAmis();
        $config = $this->getConfig();

        [$view, $data] = $config->getLayoutApp();

        return $amis->html($view, $data);
    }

    // admin 页面的 pages 接口
    public function pages()
    {
        $amis = $this->getAmis();
        $config = $this->getConfig();

        return $amis->json([
            'pages' => $config->getPageReader()->getPages()
        ]);
    }

    // pages 下的 schemaApi 请求的接口
    public function page(string $key)
    {
        $amis = $this->getAmis();
        $config = $this->getConfig();

        return $amis->json($config->getPageReader()->getPageJson($key));
    }

    // 登录页视图
    public function login()
    {
        $amis = $this->getAmis();
        $config = $this->getConfig();

        [$view, $data] = $config->getLayoutLogin();

        return $amis->html($view, $data);
    }

    abstract protected function getConfig(): Config;

    abstract protected function getAmis(): Amis;
}
