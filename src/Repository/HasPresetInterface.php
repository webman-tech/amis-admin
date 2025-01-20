<?php

namespace WebmanTech\AmisAdmin\Repository;

use WebmanTech\AmisAdmin\Helper\PresetsHelperInterface;

interface HasPresetInterface
{
    /**
     * 获取 PresetsHelper
     * @return PresetsHelperInterface
     */
    public function getPresetsHelper(): PresetsHelperInterface;
}
