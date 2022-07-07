<?php

namespace Kriss\WebmanAmisAdmin\Amis;

use Kriss\WebmanAmisAdmin\Amis\Traits\ActionButtonSupport;

/**
 * 批量操作
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/crud#%E6%89%B9%E9%87%8F%E6%93%8D%E4%BD%9C
 */
class GridBatchActions extends Component
{
    use ActionButtonSupport;

    protected array $schema = [];

    /**
     * @inheritDoc
     */
    protected function setActionButton(int $index, array $schema): void
    {
        $this->schema[$index] = $schema;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        ksort($this->schema);
        $this->schema = array_filter(array_values($this->schema));
        return parent::toArray();
    }
}