<?php

namespace WebmanTech\AmisAdmin\Amis;

/**
 * 页面
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/page
 */
class Page extends Component
{
    protected array $schema = [
        'type' => 'page',
        'body' => [],
    ];

    /**
     * 修改 body 内容
     * @param int $index 用于排序，或修改已有的 body
     * @param array|Component $schema
     * @return $this
     */
    public function withBody(int $index, $schema)
    {
        $this->schema['body'][$index] = $schema;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        ksort($this->schema['body']);
        $this->schema['body'] = array_filter(array_values($this->schema['body']));
        return parent::toArray();
    }
}