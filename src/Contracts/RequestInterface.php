<?php

namespace WebmanTech\AmisAdmin\Contracts;

interface RequestInterface
{
    /**
     * 获取所有 get 参数
     * @return array
     */
    public function getAll(): array;

    /**
     * 获取所有 post 数据，包括 file
     * @return array
     */
    public function postAll(): array;

    /**
     * @param array $params
     * @return void
     */
    public function setPathParams(array $params);

    /**
     * @return array
     */
    public function getPathParams(): array;
}