<?php

namespace Kriss\WebmanAmisAdmin\Amis;

/**
 * 页面
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/page
 */
class Page extends Component
{
    protected array $schema = [
        'type' => 'page',
    ];

    /**
     * @param int $index
     * @param array|Component $schema
     * @return $this
     */
    public function withBody(int $index, $schema)
    {
        $this->schema['body'][$index] = $schema;
        return $this;
    }

    public function toArray(): array
    {
        ksort($this->schema['body']);
        $this->schema['body'] = array_filter(array_values($this->schema['body']));
        return parent::toArray();
    }
}