<?php

namespace WebmanTech\AmisAdmin\Support;

final class ClosureHelper
{
    public static function isClosure($maybeCallable): bool
    {
        return $maybeCallable instanceof \Closure;
    }

    public static function call(\Closure $closure, ...$params)
    {
        return call_user_func_array($closure, $params);
    }

    public static function getValue($maybeCallable, ...$params)
    {
        if (self::isClosure($maybeCallable)) {
            return self::call($maybeCallable, $params);
        }
        return $maybeCallable;
    }
}
