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
     * @param mixed $default
     * @param bool $solveClosure
     * @return mixed
     */
    public static function get(string $key, mixed $default = null, bool $solveClosure = false)
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

    public static function reset(): void
    {
        self::$testConfig = [];
        self::$closureCache = [];
    }

    private static ?string $viewPath = null;

    public static function getViewPath(): string
    {
        if (self::$viewPath !== null) {
            return self::$viewPath;
        }

        // 相对 app 目录的路径
        $guessPaths = [
            '../vendor/webman-tech/amis-admin/src',
            '../vendor/webman-tech/components-monorepo/packages/amis-admin/src',
        ];
        foreach ($guessPaths as $guessPath) {
            if (is_dir(app_path() . '/' . $guessPath)) {
                return self::$viewPath = $guessPath;
            }
        }

        throw new \RuntimeException('找不到 amis-admin 模板路径');
    }
}
