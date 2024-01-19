<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

use Illuminate\Database\Eloquent\Builder;

class ActionList extends BaseAction
{
    public function handle()
    {
        $query = $this->buildQuery();

        $this->solveFilter($query);
        $this->solveOrder($query);
        $this->solveWith($query);

        $data = $this->queryList($query);
        $data['items'] = $this->solveFields($data['items']);

        return $data;
    }

    protected function queryList(Builder $query): array
    {
        return [
            'items' => $query->get(),
        ];
    }
}