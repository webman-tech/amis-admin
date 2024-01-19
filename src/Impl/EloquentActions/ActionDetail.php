<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionDetail extends BaseAction
{
    public function handle()
    {
        $id = $this->getRequestPath($this->config['id_key']);
        if (!$id) {
            return [];
        }

        $query = $this->buildQuery();
        $this->solveFilter($query);

        $model = $this->findById($query, $id);
        if (!$model) {
            return [];
        }

        return $this->solveFields([$model->toArray()])[0];
    }
}
