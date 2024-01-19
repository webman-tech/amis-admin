<?php

namespace WebmanTech\AmisAdmin\DTO;

/**
 * https://aisuda.bce.baidu.com/aisuda-docs/API%E5%AF%B9%E6%8E%A5/%E7%B3%BB%E7%BB%9FAPI#%E5%88%97%E8%A1%A8%E6%8E%A5%E5%8F%A3
 */
class RequestQueryDTO
{
    private array $query;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function getFilter(): array
    {
        return array_filter(array_map('trim', $this->query), fn($item) => $item !== '' && $item !== null);
    }

    public function getPage(): int
    {
        return $this->query['page'] ?? 1;
    }

    public function getPerPage(): int
    {
        return $this->query['perPage'] ?? 10;
    }

    public function getOrderBy(): string
    {
        return $this->query['orderBy'] ?? '';
    }

    public function getOrderDir(): string
    {
        $orderDir = $this->query['orderDir'] ?? 'asc';
        return $orderDir === 'desc' ? 'desc' : 'asc';
    }

    public function getOrder(): array
    {
        $order = [];
        if ($orderBy = $this->getOrderBy()) {
            $order[$orderBy] = $this->getOrderDir();
        }

        return $order;
    }

    public function getKeywords(): array
    {
        $keywords = $this->query['__keywords'] ?? [];
        $data = [];
        foreach ($keywords as $attributes => $search) {
            foreach (explode(',', $attributes) as $attribute) {
                $data[$attribute] = $search;
            }
        }

        return $data;
    }

    public function getRelations(): array
    {
        $value = $this->query['__relations'] ?? [];
        if (is_array($value)) {
            return $value;
        }
        return array_filter(explode(',', $value));
    }

    public function getFields(): array
    {
        $value = $this->query['__fields'] ?? [];
        if (is_array($value)) {
            return $value;
        }
        return array_filter(explode(',', $value));
    }
}