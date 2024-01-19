<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionCreate extends ActionForm
{
    public function handle()
    {
        $this->config['model_method'] ??= 'save';

        return parent::handle();
    }
}
