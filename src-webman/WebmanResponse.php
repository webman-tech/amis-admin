<?php

namespace WebmanTech\AmisAdmin\Webman;

use Webman\Http\Response;
use WebmanTech\AmisAdmin\Contracts\ResponseInterface;
use WebmanTech\AmisAdmin\Support\JsonHelper;

class WebmanResponse implements ResponseInterface
{
    /**
     * @param array $data
     * @inheritDoc
     */
    public function json(array $data)
    {
        return new Response(200, ['Content-Type' => 'application/json'], JsonHelper::encode($data));
    }

    /**
     * @inheritDoc
     */
    public function html(string $body)
    {
        return new Response(200, ['Content-Type' => 'text/html'], $body);
    }
}
