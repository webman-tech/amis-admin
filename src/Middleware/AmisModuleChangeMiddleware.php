<?php

namespace WebmanTech\AmisAdmin\Middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;

class AmisModuleChangeMiddleware implements MiddlewareInterface
{
    public function __construct(protected string $moduleName)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        /* @phpstan-ignore-next-line */
        $request->{ConfigHelper::AMIS_MODULE} = $this->moduleName;

        return $handler($request);
    }
}
