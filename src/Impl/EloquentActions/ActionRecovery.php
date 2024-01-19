<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionRecovery extends ActionIdForm
{
    public function handle()
    {
        $this->config['model_method'] ??= 'restore';

        return parent::handle();
    }
}
