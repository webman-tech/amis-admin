<?php

namespace WebmanTech\AmisAdmin\Helper;

/**
 * @internal
 */
final class ConfigHelper
{
    public const AMIS_MODULE = '__amis_module';

    public static bool $isForTest = false; // 是否为测试
    public static array $testConfig = []; // 测试配置
    private static array $closureCache = []; // 闭包缓存

    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @param bool $solveClosure
     * @return mixed
     */
    public static function get(string $key, $default = null, bool $solveClosure = false)
    {
        $module = request()->{self::AMIS_MODULE} ?? 'amis';
        $cacheKey = "{$module}.{$key}";
        if (isset(self::$closureCache[$cacheKey])) {
            return self::$closureCache[$cacheKey];
        }

        if (self::$isForTest) {
            $value = self::$testConfig[$key] ?? $default;
        } else {
            $value = config("plugin.webman-tech.amis-admin.{$module}.{$key}", $default);
        }

        if ($solveClosure && $value instanceof \Closure) {
            $value = $value();
            self::$closureCache[$cacheKey] = $value;
        }

        return $value;
    }

    public static function reset()
    {
        self::$testConfig = [];
        self::$closureCache = [];
    }
}