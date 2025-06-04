<?php

namespace WebmanTech\AmisAdmin\Repository;

use WebmanTech\AmisAdmin\Helper\PresetsHelper;

interface HasPresetInterface
{
    /**
     * 获取 PresetsHelper
     * @return PresetsHelper
     */
    public function getPresetsHelper(): PresetsHelper;
}
