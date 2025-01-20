<?php

namespace WebmanTech\AmisAdmin\Repository;

use WebmanTech\AmisAdmin\Helper\PresetsHelperInterface;

trait HasPresetTrait
{
    protected ?PresetsHelperInterface $presetsHelper = null;

    /**
     * 获取 PresetsHelper
     * @return PresetsHelperInterface
     */
    public function getPresetsHelper(): PresetsHelperInterface
    {
        if ($this->presetsHelper === null) {
            $this->presetsHelper = $this->createPresetsHelper();
        }
        return $this->presetsHelper;
    }

    /**
     * 创建 PresetsHelper
     * @return PresetsHelperInterface
     */
    abstract protected function createPresetsHelper(): PresetsHelperInterface;
}
