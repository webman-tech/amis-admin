<?php

namespace WebmanTech\AmisAdmin\Facades;

use WebmanTech\AmisAdmin\Amis as AmisStruct;
use WebmanTech\AmisAdmin\Amis\Traits\TypeComponentTrait;
use WebmanTech\AmisAdmin\Helper\ContainerHelper;

class Amis
{
    use TypeComponentTrait;

    private static ?AmisStruct $instance = null;

    public static function instance(): AmisStruct
    {
        if (static::$instance === null) {
            static::$instance = ContainerHelper::make(AmisStruct::class);
        }

        return static::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        if (static::isCallType($name)) {
            return static::callType($name, $arguments);
        }

        return static::instance()->{$name}(...$arguments);
    }
}