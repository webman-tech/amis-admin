<?php

namespace WebmanTech\AmisAdmin\Contracts;

interface ResponseInterface
{
    /**
     * 响应 json
     * @return mixed
     */
    public function json(array $data);

    /**
     * @param string $body
     * @return mixed
     */
    public function html(string $body);
}
