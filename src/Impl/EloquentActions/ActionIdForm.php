<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

class ActionIdForm extends BaseAction
{
    public function handle()
    {
        $id = $this->getRequestPath($this->config['id_key']);
        if (!$id) {
            return [];
        }
        $ids = array_filter(explode(',', $id));

        $data = $this->getPostData();
        $data = $this->solveValidate($data);

        $query = $this->buildQuery();
        $this->solveFilter($query);

        $models = $this->findAllByIds($query, $ids);

        $result = [];
        foreach ($models as $model) {
            $this->solveAssign($model, $data);
            $result[$id] = $this->solveModelMethod($model);
        }

        return $result;
    }
}