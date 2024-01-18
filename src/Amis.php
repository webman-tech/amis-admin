<?php

namespace WebmanTech\AmisAdmin;

use WebmanTech\AmisAdmin\Support\ViewRender;

class Amis
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/types/api?page=1#%E6%8E%A5%E5%8F%A3%E8%BF%94%E5%9B%9E%E6%A0%BC%E5%BC%8F-%E9%87%8D%E8%A6%81-
     * @param array $data
     * @param string $msg
     * @param array $extra
     * @return mixed
     */
    public function json(array $data, string $msg = '', array $extra = [])
    {
        $data = array_merge([
            'status' => $msg ? 1 : 0,
            'msg' => $msg,
            'data' => $data ?: '{}',
        ], $extra);

        return $this->config->getResponse()->json($data);
    }

    /**
     * 渲染 html
     * @param string $view
     * @param array $data
     * @return mixed
     */
    public function html(string $view, array $data = [])
    {
        return $this->config->getResponse()->html(
            ViewRender::renderPHPTemplate($view, $data)
        );
    }
}
