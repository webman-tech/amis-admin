<?php

namespace WebmanTech\AmisAdmin\Helper;

use support\Container;

/**
 * @internal
 */
final class ContainerHelper
{
    /**
     * 获取单例
     * @template T of class-string
     * @param T $className
     * @return T
     */
    public static function getSingleton(string $className)
    {
        if (Container::has($className)) {
            return Container::get($className);
        }
        $container = Container::instance();
        if ($container instanceof \Illuminate\Container\Container) {
            $container->singleton($className);
        }

        return $container->get($className);
    }

    /**
     * 创建新的
     * @template T of class-string
     * @param T $className
     * @param array $params
     * @return T
     */
    public static function make(string $className, array $params = [])
    {
        return Container::make($className, $params);
    }
}