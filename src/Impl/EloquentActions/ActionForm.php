<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionForm extends BaseAction
{
    public function handle()
    {
        $data = $this->getPostData();
        $data = $this->solveValidate($data);

        $this->solveAssign($this->model, $data);
        return $this->solveModelMethod($this->model);
    }
}