<?php

namespace WebmanTech\AmisAdmin\Model;

use Closure;
use Illuminate\Support\Str;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;

/**
 * @property-read string|null $label
 * @property-read string|null $labelRemark
 * @property-read string|null $description
 * @property-read Closure|null $filter
 * @property-read GridColumn|null $grid
 * @property-read FormField|null $form
 * @property-read array|null $rule
 * @property-read array|null $ruleMessages
 * @property-read array|null $ruleCustomAttribute
 * @property-read DetailAttribute|null $detail
 */
class AmisAttributeDefineItem
{
    private const NULL_VALUE = '%NULL%';

    protected string $attribute;
    protected array $define;
    protected array $attributes = [];

    public function __construct(string $attribute, array $define)
    {
        $this->attribute = $attribute;
        $this->define = array_merge([
            'label' => null,
            'labelRemark' => null,
            'description' => null,
            'filter' => true,
            'grid' => true,
            'gridExt' => null,
            'gridExtDynamic' => null,
            'form' => true,
            'formExt' => null,
            'formExtDynamic' => null,
            'rule' => 'nullable', // 默认为 nullable，使得不填 rule 时可以正常提交和传递数据
            'ruleMessages' => null,
            'ruleCustomAttribute' => null,
            'detail' => true,
            'detailExt' => null,
            'detailExtDynamic' => null,
            'selectOptions' => null,
            'selectOptionsDynamic' => null,
        ], $define);
    }

    protected string $scene = 'default';

    public function withScene(?string $scene)
    {
        $this->scene = $scene ?? 'default';
        return $this;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            if (!isset($this->define[$name])) {
                throw new \InvalidArgumentException("Undefined property: $name");
            }
            $value = value($this->define[$name], $this->attribute);
            if ($value === null) {
                $value = self::NULL_VALUE;
            } else {
                $methodName = 'build' . ucfirst($name);
                if (method_exists($this, $methodName)) {
                    $value = $this->{$methodName}($value);
                }
            }

            $this->attributes[$name] = $value;
        }

        $value = $this->attributes[$name];
        if ($value instanceof Component) {
            $this->callExt($name . 'ExtDynamic', $value, true);
        }

        return $value;
    }

    protected function buildFilter($value)
    {
        if ($value === '=' || $value === true) {
            return fn($query, $value, $attribute) => $query->where($attribute, $value);
        }
        if ($value === 'like') {
            return fn($query, $value, $attribute) => $query->where($attribute, 'like', '%' . $value . '%');
        }
        if ($value === 'datetime-range') {
            return fn($query, $value, $attribute) => $query
                ->whereBetween($attribute, array_map(
                    fn($timestamp) => date('Y-m-d H:i:s', (int)$timestamp),
                    explode(',', $value)
                ));
        }
        return $value;
    }

    protected function buildGrid($value)
    {
        if ($value === true) {
            $value = GridColumn::make()->name($this->attribute);
            /*if (($selectOptions = $this->getSelectOptionsFromColumnConfig($columnConfig)) !== null) {
                $value->typeMapping(['map' => $selectOptions['map']]);
            }*/
            if ($this->filter !== null) {
                $value = $value->searchable();
            }
        }
        if ($value instanceof GridColumn) {
            $value = $this->callExt('gridExt', $value);
        }
        return $value;
    }

    protected function buildForm($value)
    {
        if ($value === true) {
            $value = FormField::make();
        }
        if ($value instanceof FormField) {
            $value->label($this->label)
                ->labelRemark($this->labelRemark)
                ->description($this->description);
            if (in_array('required', $this->rules, true)) {
                $value->required();
            }
            $this->callExt('formExt',$value);
        }
    }

    protected function buildDetail($value)
    {
        if ($value === true) {
            $value = DetailAttribute::make();
        }
        if ($value instanceof DetailAttribute) {
            $value->label($this->label);
        }
    }

    protected function buildRule($value)
    {
        if (is_string($value)) {
            $value = [$value];
        }
        return $value;
    }

    protected function getSelectOptionsFromColumnConfig(array &$columnConfig): ?array
    {
        if ($columnConfig['selectOptions'] === null) {
            return null;
        }
        if (is_array($columnConfig['selectOptions']) && isset($columnConfig['selectOptions']['map'])) {
            return $columnConfig['selectOptions'];
        }
        if ($columnConfig['selectOptions'] instanceof Closure) {
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

    protected function callExt(string $name, Component $component, bool $useScene = false)
    {
        $ext = $this->define[$name] ?? null;
        if ($ext) {
            if ($useScene) {
                $result = $ext($component, $this->scene);
            } else {
                $result = $ext($component);
            }
            if ($result !== null) {
                return $result;
            }
        }
        return $component;
    }
}