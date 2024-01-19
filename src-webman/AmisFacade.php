<?php

namespace WebmanTech\AmisAdmin\Webman;

use Webman\Route;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Config;
use WebmanTech\AmisAdmin\Contracts\RequestInterface;
use WebmanTech\AmisAdmin\Contracts\ResponseInterface;
use WebmanTech\AmisAdmin\Helper\ArrayHelper;

class AmisFacade
{
    public const AMIS_MODULE = '__amis_module';

    protected static array $amisInstance = [];

    public static function amis(): Amis
    {
        $module = static::getModule();
        if (!isset(static::$amisInstance[$module])) {
            static::$amisInstance[$module] = new Amis(static::config());
        }

        return static::$amisInstance[$module];
    }

    protected static array $configInstance = [];

    public static function config(): Config
    {
        $module = static::getModule();
        if (!isset(static::$configInstance[$module])) {
            static::$configInstance[$module] = new Config(ArrayHelper::merge(
                [
                    RequestInterface::class => WebmanRequest::class,
                    ResponseInterface::class => WebmanResponse::class,
                ],
                config("plugin.webman-tech.amis-admin.amis.modules.{$module}"),
            ));
        }

        return static::$configInstance[$module];
    }

    public static function registerRoutes(): void
    {
        foreach (config('plugin.webman-tech.amis-admin.amis.modules') as $name => $module) {
            Route::group('/' . $name, function () {
                Route::get('/', [AdminController::class, 'layoutApp']);
                Route::get('/login', [AdminController::class, 'login']);
                Route::get('/pages', [AdminController::class, 'pages']);
                Route::get('/page/{key}', [AdminController::class, 'page']);
            })->middleware([
                fn() => new AmisModuleChangeMiddleware($name),
            ]);
        }
    }

    protected static function getModule(): string
    {
        return request()->{static::AMIS_MODULE} ?? 'admin';
    }
}
