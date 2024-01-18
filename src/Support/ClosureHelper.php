<?php

namespace WebmanTech\AmisAdmin\Support;

final class ClosureHelper
{
    public static function getValue($maybeCallable)
    {
        if (!$maybeCallable) {
            return $maybeCallable;
        }
        if ($maybeCallable instanceof \Closure) {
            return call_user_func($maybeCallable);
        }
        return $maybeCallable;
    }
}
