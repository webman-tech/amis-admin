<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Amis\Traits\ComponentCommonFn;

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
 * @method $this typeTpl(array $schema = [])
 */
class GridColumn extends Component
{
    use ComponentCommonFn;

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
        $this->solveType();
        $this->solveSearchable();
        $this->solveQuickEdit();

        return parent::toArray();
    }

    /**
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        if (strlen($name) > 4 && str_starts_with($name, 'type')) {
            $this->schema['type'] = lcfirst(substr($name, 4));
            $this->schema($arguments[0] ?? []);
        } else {
            $this->callToSetSchema($name, $arguments);
        }
        return $this;
    }

    protected function solveType(): void
    {
        $type = $this->schema['type'];
        if ($type === 'mapping') {
            $this->solveMappingMap();
        }
    }

    protected function solveSearchable(): void
    {
        $searchable = $this->schema['searchable'] ?? false;
        if (!$searchable) {
            return;
        }

        $autoSolve = !is_array($searchable);
        if ($autoSolve) {
            $searchable = ['type' => 'input-text'];
        }
        $searchable['name'] ??= $this->schema['name'];
        $searchable['clearable'] ??= true;

        if (!$autoSolve) {
            $this->schema['searchable'] = $searchable;
            return;
        }
        $type = $this->schema['type'];
        if ($type === 'mapping') {
            if (isset($this->schema['map'])) {
                $searchable = array_merge(
                    $this->buildTypeSelectBySchemaMap($this->schema['map']),
                    $searchable
                );
            }
        } elseif ($type === 'date' || $type === 'datetime') {
            $searchable['type'] = 'input-datetime-range';
        }

        $this->schema['searchable'] = $searchable;
    }

    protected function solveQuickEdit(): void
    {
        if (!isset($this->schema['quickEdit'])) {
            return;
        }
        $quickEdit = $this->schema['quickEdit'];
        if ($quickEdit === true) {
            $quickEdit = [];
        }

        $type = $this->schema['type'];
        if ($type === 'mapping') {
            if (isset($this->schema['map'])) {
                $quickEdit = array_merge(
                    $this->buildTypeSelectBySchemaMap($this->schema['map']),
                    $quickEdit
                );
            }
        }

        $this->schema['quickEdit'] = $quickEdit;
    }

    private function buildTypeSelectBySchemaMap(array $map): array
    {
        return [
            'type' => 'select',
            'options' => array_map(
                function (array $item) {
                    if (isset($item['label'])) {
                        $item['label'] = strip_tags((string)$item['label']); // 去除 html 结构，使得 map 带 html 格式时支持
                    }
                    return $item;
                },
                $map,
            ),
        ];
    }
}
