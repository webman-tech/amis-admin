<?php

namespace WebmanTech\AmisAdmin\Contracts;

use WebmanTech\AmisAdmin\Config;

interface PageReaderInterface
{
    /**
     * @see https://baidu.github.io/amis/zh-CN/components/app#属性说明
     * @return array
     */
    public function getPages(): array;

    /**
     * @see https://baidu.github.io/amis/zh-CN/components/page
     * @param string $key
     * @return array
     */
    public function getPageJson(string $key): array;

    /**
     * @param Config $config
     * @return self
     */
    public function withConfig(Config $config);
}
