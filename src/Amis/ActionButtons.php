<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Amis\Traits\ActionButtonSupport;

class ActionButtons extends Component
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