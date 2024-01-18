<?php

namespace WebmanTech\AmisAdmin\Webman;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Config;
use WebmanTech\AmisAdmin\Traits\AdminControllerTrait;

class AdminController
{
    use AdminControllerTrait;

    protected function getConfig(): Config
    {
        return AmisFacade::config();
    }

    protected function getAmis(): Amis
    {
        return AmisFacade::amis();
    }
}
