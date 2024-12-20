<?php

namespace WebmanTech\AmisAdmin\Facades;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\Traits\TypeComponentTrait;
use WebmanTech\AmisAdmin\Helper\ContainerHelper;

/**
 * @method static Response response(array $data, string $msg = '', array $extra = [])
 * @method static string renderApp(array $schema = [])
 * @method static string renderPage(string $title, array $schema = [])
 * @method static string getRequestPath(Request $request)
 */
class AmisFacade
{
    use TypeComponentTrait;

    private static ?Amis $instance = null;

    public static function instance(): Amis
    {
        if (static::$instance === null) {
            static::$instance = ContainerHelper::make(Amis::class);
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