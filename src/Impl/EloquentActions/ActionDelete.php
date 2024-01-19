<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionDelete extends ActionIdForm
{
    public function handle()
    {
        $this->config['model_method'] ??= 'delete';

        return parent::handle();
    }
}
