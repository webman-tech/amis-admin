<?php

namespace WebmanTech\AmisAdmin\Controller;

use support\Container;
use support\Context;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Route;
use WebmanTech\AmisAdmin\Amis\ActionPage\CrudCreateActionPage;
use WebmanTech\AmisAdmin\Amis\ActionPage\CrudIndexActionPage;
use WebmanTech\AmisAdmin\Amis\Service\DynamicPage;
use WebmanTech\AmisAdmin\Amis\Service\DynamicFormPage;

/**
 * Amis 动态页面控制器
 */
class AmisDynamicPageController
{
    public static string $requestServiceKey = '_service_key';

    public static function registerRoute()
    {
        Route::post('/amis-dynamic-service/page', [static::class, 'page'])->name('amis-dynamic-service.page');
        Route::post('/amis-dynamic-service/handle', [static::class, 'handle'])->name('amis-dynamic-service.handle');
    }

    public static function registerCrudServiceBind(array $models)
    {
        foreach ($models as $model) {
            self::registerServiceBind([
                $model . '.index' => fn() => new CrudIndexActionPage(),
                $model . '.create' => fn() => new CrudCreateActionPage(),
            ]);
        }
    }

    public static function registerServiceBind(array $binds)
    {
        foreach ($binds as $key => $service) {
            Context::set($key, $service);
        }
    }

    public static function getPageSchemaApi(string $serviceKey)
    {
        return [
            'url' => route('amis-dynamic-service.page'),
            'method' => 'post',
            'data' => [
                static::$requestServiceKey => $serviceKey,
            ],
        ];
    }

    public static function getHandleSchemaApi(string $serviceKey)
    {
        return [
            'url' => route('amis-dynamic-service.handle'),
            'method' => 'post',
            'data' => [
                static::$requestServiceKey => $serviceKey,
            ],
        ];
    }

    public function page(Request $request)
    {
        $serviceKey = $request->post(static::$requestServiceKey);
        $service = Context::get($serviceKey) ?? fn() => Container::get($serviceKey);
        $service = value($service);
        if (!$service instanceof DynamicPage) {
            throw new \InvalidArgumentException('service invalid');
        }

        return $service->responsePage();
    }

    public function handle(Request $request)
    {
        $serviceKey = $request->post(static::$requestServiceKey);
        $service = Context::get($serviceKey) ?? fn() => Container::get($serviceKey);
        $service = value($service);
        if (!$service instanceof DynamicFormPage) {
            throw new \InvalidArgumentException('service invalid');
        }

        $result = $service->handleSubmit($request);
        if ($result instanceof Response) {
            return $result;
        }
        return amis_response($result ?? []);
    }
}