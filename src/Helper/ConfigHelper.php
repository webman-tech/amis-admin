<?php

namespace Kriss\WebmanAmisAdmin\Helper;

class ConfigHelper
{
    public const AMIS_MODULE = '__amis_module';

    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $module = request()->{self::AMIS_MODULE} ?? 'amis';
        return config("plugin.webman-tech.amis-admin.{$module}.{$key}", $default);
    }
}