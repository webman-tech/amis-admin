<?php

namespace WebmanTech\AmisAdmin\Amis;

/**
 * table 的单个 column
 *
 * @method $this name($value)
 * @method $this label($value)
 * @method $this type($value)
 * @method $this sortable(bool $is = true)
 * @method $this searchable(bool|array $schema = true)
 * @method $this quickEdit(bool|array $schema = true)
 * @method $this copyable(bool|array $schema = true)
 * @method $this width(string $value)
 * @method $this fixed(string $value) left | right | none
 * @method $this remark(string|array $value)
 * @method $this align(string $value) left | right | center
 * @method $this toggled(bool $is) 默认是否展示
 *
 * @method $this typeCode(array $schema = [])
 * @method $this typeColor(array $schema = [])
 * @method $this typeDate(array $schema = [])
 * @method $this typeDatetime(array $schema = [])
 * @method $this typeHtml(array $schema = [])
 * @method $this typeImage(array $schema = [])
 * @method $this typeImages(array $schema = [])
 * @method $this typeJson(array $schema = [])
 * @method $this typeLink(array $schema = [])
 * @method $this typeMapping(array $schema)
 * @method $this typeProgress(array $schema = [])
 * @method $this typeQrcode(array $schema = [])
 * @method $this typeBarcode(array $schema = [])
 * @method $this typeTag(array $schema = [])
 * @method $this typeAvatar(array $schema = [])
 */
class GridColumn extends Component
{
    protected array $schema = [
        'type' => 'text',
        'name' => '',
        'align' => 'center',
    ];

    protected array $defaultValue = [
        'sortable' => true,
        'searchable' => true,
        'quickEdit' => true,
        'copyable' => true,
    ];

    /**
     * 截断
     * @param int $size
     * @param array|bool $popOver
     * @return GridColumn
     */
    public function truncate(int $size = 20, $popOver = null)
    {
        $schema = [
            'type' => 'tpl',
            'tpl' => "\${{$this->schema['name']}|truncate:{$size}}",
        ];
        if ($popOver !== false) {
            $schema['popOverEnableOn'] = "this.{$this->schema['name']} && this.{$this->schema['name']}.length > {$size}";
            if (is_array($popOver)) {
                $schema['popOver'] = $this->merge([
                    'showIcon' => true,
                    'body' => [
                        'type' => 'tpl',
                        'tpl' => "\${{$this->schema['name']}}",
                    ],
                ], $popOver);
            } else {
                $schema['popOver'] = "\${{$this->schema['name']}}";
            }
        }
        return $this->schema($schema);
    }

    public function toArray(): array
    {
        $this->solveSearchable();
        $this->solveQuickEdit();

        return parent::toArray();
    }

    public function __call($name, $arguments)
    {
        if (strlen($name) > 4 && strpos($name, 'type') === 0) {
            $this->schema['type'] = lcfirst(substr($name, 4));
            $this->schema($arguments[0] ?? []);
        } else {
            $value = $arguments[0] ?? null;
            if ($value === null) {
                $value = $this->defaultValue[$name] ?? null;
            }
            $this->schema[$name] = $value;
        }
        return $this;
    }

    protected function solveSearchable()
    {
        $searchable = $this->schema['searchable'] ?? false;
        if (!$searchable) {
            return;
        }

        $autoSolve = !is_array($searchable);
        if ($autoSolve) {
            $searchable = ['type' => 'input-text'];
        }
        $searchable['name'] = $searchable['name'] ?? $this->schema['name'];
        $searchable['clearable'] = $searchable['clearable'] ?? true;

        if (!$autoSolve) {
            $this->schema['searchable'] = $searchable;
            return;
        }
        $type = $this->schema['type'];
        if ($type === 'mapping') {
            if (isset($this->schema['map'])) {
                $searchable['type'] = 'select';
                $searchable['options'] = array_map(
                    fn($label, $value) => [
                        'label' => strip_tags($label),
                        'value' => $value,
                    ],
                    array_values($this->schema['map']),
                    array_keys($this->schema['map'])
                );
            }
        } elseif ($type === 'date' || $type === 'datetime') {
            $searchable['type'] = 'input-datetime-range';
        }

        $this->schema['searchable'] = $searchable;
    }

    protected function solveQuickEdit()
    {
        $quickEdit = $this->schema['quickEdit'] ?? false;
        if (!$quickEdit || is_array($quickEdit)) {
            return;
        }

        $type = $this->schema['type'];
        if ($type === 'mapping') {
            if (isset($this->schema['map'])) {
                $quickEdit = [];
                $quickEdit['type'] = 'select';
                $quickEdit['options'] = array_map(
                    fn($label, $value) => [
                        'label' => strip_tags($label),
                        'value' => $value,
                    ],
                    array_values($this->schema['map']),
                    array_keys($this->schema['map'])
                );
            }
        }

        $this->schema['quickEdit'] = $quickEdit;
    }
}