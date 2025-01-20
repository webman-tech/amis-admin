<?php

namespace WebmanTech\AmisAdmin\Helper;

use Illuminate\Support\Collection;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;

class PresetsHelper implements PresetsHelperInterface
{
    protected array $presets;
    protected ?array $defaultEnable = null;

    public function __construct(array $presets = [])
    {
        $this->presets = $presets;
    }

    /**
     * @inheritDoc
     */
    public function withPresets(array $presets)
    {
        $this->presets = array_merge($this->presets, $presets);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withDefaultEnable(?array $keys = null)
    {
        $this->defaultEnable = $keys;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pickLabel(?array $keys = null): array
    {
        return $this->pickColumn('label', $keys, null, true);
    }

    /**
     * @inheritDoc
     */
    public function pickFilter(?array $keys = null): array
    {
        return $this->pickColumn('filter', $keys, function ($v) {
            if ($v === '=') {
                return fn($query, $value, $attribute) => $query->where($attribute, $value);
            }
            if ($v === 'like') {
                return fn($query, $value, $attribute) => $query->where($attribute, 'like', '%' . $value . '%');
            }
            if ($v === 'datetime-range') {
                return fn($query, $value, $attribute) => $query
                    ->whereBetween($attribute, array_map(
                        fn($timestamp) => date('Y-m-d H:i:s', (int)$timestamp),
                        explode(',', $value)
                    ));
            }
            return $v;
        }, true);
    }

    /**
     * @inheritDoc
     */
    public function pickGrid(?array $keys = null): array
    {
        return $this->pickColumn('grid', $keys, function ($v, string $column) {
            if ($v === null) {
                return null;
            }
            return $v($column);
        });
    }

    /**
     * @inheritDoc
     */
    public function pickForm(string $scene, ?array $keys = null): array
    {
        $items = $this->pickColumn('form', $keys, function ($v, string $column) use ($scene) {
            if ($v === null) {
                return null;
            }
            return $v($column, $scene);
        });
        // 允许同时展示两个 FormItem
        $data = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $data = array_merge($data, $item);
            } else {
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function pickRules(string $scene, ?array $keys = null): array
    {
        return $this->pickColumn('rule', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($column, $scene);
            }
            return $v;
        }, true);
    }

    /**
     * @inheritDoc
     */
    public function pickRuleMessages(string $scene, ?array $keys = null): array
    {
        $items = $this->pickColumn('ruleMessages', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($column, $scene);
            }
            return $v;
        });
        $data = [];
        foreach ($items as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function pickRuleCustomAttributes(string $scene, ?array $keys = null): array
    {
        return $this->pickColumn('ruleCustomAttribute', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($column, $scene);
            }
            return $v;
        }, true);
    }

    /**
     * @inheritDoc
     */
    public function pickDetail(?array $keys = null): array
    {
        return $this->pickColumn('detail', $keys, function ($v, string $column) {
            if ($v === true) {
                return $column;
            }
            if ($v instanceof \Closure) {
                return $v($column);
            }
            return $v;
        });
    }

    protected ?Collection $formattedPresets = null;

    protected function pickColumn(string $type, ?array $keys = null, ?callable $fnForValue = null, bool $keepKey = false): array
    {
        if ($this->formattedPresets === null) {
            $this->formattedPresets = collect($this->presets)
                ->map(function (array $item) {
                    return array_merge($this->getDefaultColumnConfig(), $item);
                });
        }
        if ($keys === null) {
            if ($this->defaultEnable === null) {
                $this->defaultEnable = $this->formattedPresets->keys()->toArray();
            }
            if ($this->defaultEnable) {
                $keys = $this->defaultEnable;
            }
        }

        $data = $this->formattedPresets
            ->only($keys)
            ->map(function (array $item, string $key) use ($type, $fnForValue) {
                $v = $item[$type];
                if ($fnForValue !== null) {
                    $v = $fnForValue($v, $key);
                }
                return $v;
            })
            ->filter(fn($v) => $v !== null)
            ->only($keys)
            ->toArray();

        // 按照给定的 key 排序
        if ($keys !== null) {
            $data = array_merge(array_flip(array_intersect($keys, array_keys($data))), $data);
        }

        return $keepKey ? $data : array_values($data);
    }

    protected function getDefaultColumnConfig(): array
    {
        return [
            'label' => null,
            'filter' => '=',
            'grid' => fn(string $column) => GridColumn::make()->name($column)->searchable(),
            'form' => fn(string $column) => FormField::make()->name($column),
            'rule' => 'nullable',
            'ruleMessages' => null,
            'ruleCustomAttribute' => null,
            'detail' => true,
        ];
    }
}
