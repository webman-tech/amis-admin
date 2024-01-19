<?php

namespace WebmanTech\AmisAdmin\Webman;

use WebmanTech\AmisAdmin\Contracts\RequestInterface;

class WebmanRequest implements RequestInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return request()->get();
    }

    /**
     * @inheritDoc
     */
    public function postAll(): array
    {
        return array_merge_recursive(request()->post(), request()->file());
    }

    protected array $pathParams = [];

    /**
     * @inheritDoc
     */
    public function setPathParams(array $params)
    {
        $this->pathParams = $params;
    }

    /**
     * @inheritDoc
     */
    public function getPathParams(): array
    {
        return $this->pathParams;
    }
}