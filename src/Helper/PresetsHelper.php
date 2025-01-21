<?php

namespace WebmanTech\AmisAdmin\Helper;

use Illuminate\Support\Collection;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class PresetsHelper
{
    protected array $presets;
    protected ?bool $defaultNoEdit = null;
    protected array $sceneKeys = [];

    /**
     * @param array $presets
     */
    public function __construct(array $presets = [])
    {
        $this->presets = $presets;
    }

    /**
     * 添加预设
     * @param array $presets
     * @return $this
     */
    public function withPresets(array $presets)
    {
        $this->presets = array_merge($this->presets, $presets);
        return $this;
    }

    /**
     * 默认不启用编辑（只读）
     * @return $this
     */
    public function withDefaultNoEdit()
    {
        $this->defaultNoEdit = true;
        return $this;
    }

    /**
     * 设置 CRUD 场景对应的字段
     * @param array $keys
     * @return $this
     */
    public function withCrudSceneKeys(array $keys)
    {
        return $this->withSceneKeys([
            AbsRepository::SCENE_LIST => $keys,
            AbsRepository::SCENE_CREATE => $keys,
            AbsRepository::SCENE_UPDATE => $keys,
            AbsRepository::SCENE_DETAIL => $keys,
        ]);
    }

    /**
     * 设置场景对应的字段
     * @param array $data
     * @return $this
     */
    public function withSceneKeys(array $data)
    {
        foreach ($data as $scene => $keys) {
            $this->sceneKeys[$scene] = $keys;
        }
        return $this;
    }

    /**
     * 获取字段对应的 label
     * @param array|null $keys
     * @return array
     */
    public function pickLabel(?array $keys = null): array
    {
        return $this->pickColumn(null, 'label', $keys, null, true);
    }

    /**
     * 获取字段对应的 labelRemark
     * @param array|null $keys
     * @return array
     */
    public function pickLabelRemark(?array $keys = null): array
    {
        return $this->pickColumn(null, 'labelRemark', $keys, null, true);
    }

    /**
     * 获取字段对应的 description
     * @param array|null $keys
     * @return array
     */
    public function pickDescription(?array $keys = null): array
    {
        return $this->pickColumn(null, 'description', $keys, null, true);
    }

    /**
     * 获取字段对应的 filter
     * @param array|null $keys
     * @return array
     */
    public function pickFilter(?array $keys = null): array
    {
        return $this->pickColumn(AbsRepository::SCENE_LIST, 'filter', $keys, function ($v) {
            if ($v === '=' || $v === true) {
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
     * 获取字段对应的 grid
     * @param array|null $keys
     * @return array
     */
    public function pickGrid(?array $keys = null): array
    {
        return $this->pickColumn(AbsRepository::SCENE_LIST, 'grid', $keys, function ($v, string $column, array $columnConfig) {
            if ($v === null) {
                return null;
            }
            if ($v === true) {
                $value = GridColumn::make()->name($column);
                if (($selectOptions = $this->getSelectOptionsFromColumnConfig($columnConfig)) !== null) {
                    $value->typeMapping(['map' => $selectOptions['map']]);
                }
                if ($this->isColumnSearchable($column)) {
                    $value->searchable();
                }
                if (($ext = $columnConfig['gridExt']) instanceof \Closure) {
                    $ext($value);
                }
                return $value;
            }
            return $v($column);
        });
    }

    /**
     * 获取字段对应的 form
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickForm(string $scene, ?array $keys = null): array
    {
        $items = $this->pickColumn($scene, 'form', $keys, function ($v, string $column, array $columnConfig) use ($scene) {
            if ($v === null) {
                return null;
            }
            if ($v === true) {
                $value = FormField::make()->name($column);
                if (($selectOptions = $this->getSelectOptionsFromColumnConfig($columnConfig)) !== null) {
                    $value->typeSelect(['options' => $selectOptions['options']]);
                }
                if ($this->isColumnRequired($column, $scene)) {
                    $value->required();
                }
                if (($ext = $columnConfig['formExt']) instanceof \Closure) {
                    $ext($value, $scene);
                }
                return $value;
            }
            return $v($column, $scene);
        });
        // 允许同时展示多个 FormItem
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
     * 获取字段对应的 rules
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRules(string $scene, ?array $keys = null): array
    {
        return $this->pickColumn($scene, 'rule', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($scene, $column);
            }
            return $v;
        }, true);
    }

    /**
     * 获取字段对应的 ruleMessages
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRuleMessages(string $scene, ?array $keys = null): array
    {
        $items = $this->pickColumn($scene, 'ruleMessages', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($scene, $column);
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
     * 获取字段对应的 ruleCustomAttributes
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRuleCustomAttributes(string $scene, ?array $keys = null): array
    {
        return $this->pickColumn($scene, 'ruleCustomAttribute', $keys, function ($v, string $column) use ($scene) {
            if ($v instanceof \Closure) {
                return $v($scene, $column);
            }
            return $v;
        }, true);
    }

    /**
     * 获取字段对应的 detail
     * @param array|null $keys
     * @return array
     */
    public function pickDetail(?array $keys = null): array
    {
        return $this->pickColumn(AbsRepository::SCENE_DETAIL, 'detail', $keys, function ($v, string $column, array $columnConfig) {
            if ($v === true) {
                $value = DetailAttribute::make()->name($column);
                if (($selectOptions = $this->getSelectOptionsFromColumnConfig($columnConfig)) !== null) {
                    $value->typeMapping(['map' => $selectOptions['map']]);
                }
                if (($ext = $columnConfig['detailExt']) instanceof \Closure) {
                    $ext($value);
                }
                return $value;
            }
            if ($v instanceof \Closure) {
                return $v($column);
            }
            return $v;
        });
    }

    protected ?Collection $formattedPresets = null;

    protected function pickColumn(?string $scene, string $type, ?array $keys = null, ?callable $fnForValue = null, bool $keepKey = false): array
    {
        if ($this->formattedPresets === null) {
            $this->formattedPresets = collect($this->presets)
                ->map(function (array $item) {
                    return array_merge($this->getDefaultColumnConfig(), $item);
                });
        }
        if ($keys === null && $scene !== null) {
            $keys = $this->sceneKeys[$scene] ?? null;
        }

        $data = $this->formattedPresets
            ->only($keys)
            ->map(function (array $item, string $key) use ($type, $fnForValue) {
                $v = $item[$type];
                if ($fnForValue !== null) {
                    $v = $fnForValue($v, $key, $item);
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
            'labelRemark' => null,
            'description' => null,
            'filter' => true,
            'grid' => true,
            'gridExt' => null,
            'form' => $this->defaultNoEdit ? null : true,
            'formExt' => null,
            'selectOptions' => null,
            'rule' => $this->defaultNoEdit ? null : 'nullable', // 默认为 nullable，使得不填 rule 时可以正常提交和传递数据
            'ruleMessages' => null,
            'ruleCustomAttribute' => null,
            'detail' => true,
            'detailExt' => null,
        ];
    }

    protected function isColumnSearchable(string $column): bool
    {
        return isset($this->pickFilter([$column])[$column]);
    }

    protected function isColumnRequired(string $column, string $scene): bool
    {
        $rules = $this->pickRules($scene, [$column])[$column] ?? '';
        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }
        return in_array('required', $rules, true);
    }

    protected function getSelectOptionsFromColumnConfig(array &$columnConfig): ?array
    {
        if ($columnConfig['selectOptions'] === null) {
            return null;
        }
        if (is_array($columnConfig['selectOptions']) && isset($columnConfig['selectOptions']['map'])) {
            return $columnConfig['selectOptions'];
        }
        if ($columnConfig['selectOptions'] instanceof \Closure) {
            $columnConfig['selectOptions'] = $columnConfig['selectOptions']();
        }

        $map = $columnConfig['selectOptions'];
        $options = [];
        foreach ($map as $value => $label) {
            $options[] = [
                'value' => (string)$value, // 强制为 string，保证行为一致
                'label' => strip_tags($label), // 去除 html
            ];
        }

        return $columnConfig['selectOptions'] = [
            'map' => $map,
            'options' => $options,
            'values' => array_keys($map),
        ];
    }
}
