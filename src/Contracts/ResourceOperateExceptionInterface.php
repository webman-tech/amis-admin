<?php

namespace WebmanTech\AmisAdmin\Contracts;

interface ResourceOperateExceptionInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}