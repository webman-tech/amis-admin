<?php

namespace Kriss\WebmanAmisAdmin\Repository;

abstract class AbsRepository implements RepositoryInterface
{
    protected string $keyName = 'id';

    public function getGridRelations(): array
    {
        return [];
    }

    public function getGridColumns(): array
    {
        return ['*'];
    }

    public function getFormColumns(): array
    {
        return ['*'];
    }

    public function getDetailColumns(): array
    {
        return ['*'];
    }

    public function getLabel(string $attribute): string
    {
        return $attribute;
    }
}