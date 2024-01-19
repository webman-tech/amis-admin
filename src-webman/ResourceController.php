<?php

namespace WebmanTech\AmisAdmin\Webman;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Config;
use WebmanTech\AmisAdmin\Contracts\RepositoryInterface;
use WebmanTech\AmisAdmin\Impl\EloquentRepository;
use WebmanTech\AmisAdmin\Traits\ResourceControllerTrait;

abstract class ResourceController
{
    protected string $modelClass;

    use ResourceControllerTrait;

    protected function getConfig(): Config
    {
        return AmisFacade::config();
    }

    protected function getAmis(): Amis
    {
        return AmisFacade::amis();
    }

    protected function createRepository(): RepositoryInterface
    {
        return new EloquentRepository($this->modelClass);
    }
}
