<?php

namespace WebmanTech\AmisAdmin\Webman;

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
        $request->{AmisFacade::AMIS_MODULE} = $this->moduleName;

        return $handler($request);
    }
}