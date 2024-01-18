<?php

namespace WebmanTech\AmisAdmin\Support;

final class ViewRender
{
    /**
     * 渲染 php 模版
     * @param string $template
     * @param array $data
     * @return false|string
     */
    public static function renderPHPTemplate(string $template, array $data)
    {
        $__template_path__ = $template;
        extract($data);
        ob_start();
        try {
            include $__template_path__;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }
}
