<?php

namespace Kriss\WebmanAmisAdmin\Amis;

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
        'align' => 'center',
    ];

    protected array $defaultValue = [
        'sortable' => true,
        'searchable' => true,
        'quickEdit' => true,
        'copyable' => true,
    ];

    protected function solveMapSearch()
    {
        if (isset($this->schema['type']) && $this->schema['type'] === 'mapping' && isset($this->schema['map'])) {
            if (isset($this->schema['searchable']) && $this->schema['searchable'] !== false) {
                if (!is_array($this->schema['searchable'])) {
                    $this->schema['searchable'] = [
                        'type' => 'select',
                        'options' => array_map(
                            fn($label, $value) => [
                                'label' => $label,
                                'value' => $value,
                            ],
                            array_values($this->schema['map']),
                            array_keys($this->schema['map'])
                        )
                    ];
                }
            }
        }
    }

    public function toArray(): array
    {
        $this->solveMapSearch();

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
}