<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionUpdate extends ActionIdForm
{
    public function handle()
    {
        $this->config['model_method'] ??= 'save';

        return parent::handle();
    }
}
