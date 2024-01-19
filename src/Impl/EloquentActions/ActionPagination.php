<?php

namespace WebmanTech\AmisAdmin\Impl\EloquentActions;

use Illuminate\Database\Eloquent\Builder;

class ActionPagination extends BaseAction
{
    public function handle()
    {
        $query = $this->buildQuery();

        $this->solveFilter($query);
        $this->solveOrder($query);
        $this->solveWith($query);

        $data = $this->queryPagination($query);
        $data['items'] = $this->solveFields($data['items']);

        return $data;
    }

    protected function queryPagination(Builder $query): array
    {
        $get = $this->getRequestQueryDTO();
        $paginator = $query->paginate($get->getPerPage(), ['*'], 'page', $get->getPage());

        return [
            'items' => $paginator->items(),
            'count' => $paginator->total(),
        ];
    }
}
