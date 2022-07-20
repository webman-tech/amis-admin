<?php

namespace Kriss\WebmanAmisAdmin\Middleware;

use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AmisModuleChangeMiddleware implements MiddlewareInterface
{
    protected string $moduleName;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        $request->{ConfigHelper::AMIS_MODULE} = $this->moduleName;

        return $handler($request);
    }
}