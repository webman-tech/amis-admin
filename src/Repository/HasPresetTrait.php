<?php

namespace WebmanTech\AmisAdmin\Repository;

use WebmanTech\AmisAdmin\Helper\PresetsHelper;

trait HasPresetTrait
{
    protected ?PresetsHelper $presetsHelper = null;

    /**
     * 获取 PresetsHelper
     * @return PresetsHelper
     */
    public function getPresetsHelper(): PresetsHelper
    {
        if ($this->presetsHelper === null) {
            $this->presetsHelper = $this->createPresetsHelper();
        }
        return $this->presetsHelper;
    }

    /**
     * 创建 PresetsHelper
     * @return PresetsHelper
     */
    abstract protected function createPresetsHelper(): PresetsHelper;
}
