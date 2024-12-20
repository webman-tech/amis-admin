<?php

namespace WebmanTech\AmisAdmin\Helper;

final class ConfigHelper
{
    public const AMIS_MODULE = '__amis_module';

    /**
     * 获取模块
     * @return string
     */
    public static function getModule(): string
    {
        return request()->{self::AMIS_MODULE} ?? 'amis';
    }

    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $module = self::getModule();
        return config("plugin.webman-tech.amis-admin.{$module}.{$key}", $default);
    }

    protected static array $componentConfigs = [];

    /**
     * 获取组件的配置
     * @param string $key
     * @return array
     */
    public static function getComponentConfig(string $key): array
    {
        $module = self::getModule();
        if (!isset(self::$componentConfigs[$module][$key])) {
            $config = self::get("components.{$key}", []);
            if (is_callable($config)) {
                $config = call_user_func($config);
            }
            self::$componentConfigs[$module][$key] = (array)$config;
        }
        return self::$componentConfigs[$module][$key];
    }
}