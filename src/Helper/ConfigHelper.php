<?php

namespace Kriss\WebmanAmisAdmin\Helper;

class ConfigHelper
{
    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return config('plugin.kriss.webman-amis-admin.' . $key, $default);
    }
}