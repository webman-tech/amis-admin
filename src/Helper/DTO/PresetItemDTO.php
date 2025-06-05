<?php

namespace WebmanTech\AmisAdmin\Helper\DTO;

use Closure;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

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
class PresetItemDTO
{
    private const NULL_VALUE = '%NULL%';
    private const SCENE_DEFAULT = 'default';
    private const SCENE_UPDATE_APPEND_RULE = 'sometimes';

    protected string $key;
    protected array $define;
    protected array $attributes = [];
    protected array $config = [
        'defaultCanEdit' => true, // 默认是否可以编辑
    ];

    public function __construct(string $key, array $define, array $config = [])
    {
        $this->key = $key;
        $this->config = array_merge($this->config, $config);
        $this->define = $define;
    }

    /**
     * 全部的 define 配置
     * @return array
     */
    protected function getDefaultDefine(): array
    {
        return [
            'label' => null,
            'labelRemark' => null,
            'description' => null,
            'filter' => true,
            'grid' => true,
            'gridExt' => null,
            'gridExtDynamic' => null,
            'form' => $this->config['defaultCanEdit'] ? true : null,
            'formExt' => null,
            'formExtDynamic' => null,
            'rule' => $this->config['defaultCanEdit'] ? 'nullable' : null, // 默认为 nullable，使得不填 rule 时可以正常提交和传递数据
            'ruleMessages' => null,
            'ruleCustomAttribute' => null,
            'detail' => true,
            'detailExt' => null,
            'detailExtDynamic' => null,
            'selectOptions' => null,
        ];
    }

    /**
     * 根据 name 获取 define 配置
     * @param string $name
     * @return mixed|null
     */
    protected function getDefineValueByName(string $name)
    {
        return array_key_exists($name, $this->define)
            ? $this->define[$name]
            : ($this->getDefaultDefine()[$name] ?? null);
    }

    protected string $scene = self::SCENE_DEFAULT;

    /**
     * @return $this
     */
    public function withScene(?string $scene)
    {
        $this->scene = $scene ?? self::SCENE_DEFAULT;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            $value = value($this->getDefineValueByName($name), $this->key);
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
        $value = $this->callExt($name . 'ExtDynamic', $value, true);

        return $value === self::NULL_VALUE ? null : $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
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
        // TODO 扩展其他 filter，或者方式（比如 static 直接注入？）
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function buildGrid($value)
    {
        if ($value === true) {
            $value = GridColumn::make()
                ->name($this->key)
                ->label($this->label);
            if (($info = $this->getSelectOptionsInfo()) !== null) {
                $value->typeMapping(['map' => $info['map']]);
            }
            if ($this->filter !== null) {
                $value->searchable();
            }
        }
        if ($value instanceof Component) {
            $value = $this->callExt('gridExt', $value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function buildForm($value)
    {
        if ($value === true) {
            $value = FormField::make()
                ->name($this->key)
                ->label($this->label)
                ->labelRemark($this->labelRemark)
                ->description($this->description);
            if (($info = $this->getSelectOptionsInfo()) !== null) {
                $value->typeSelect(['options' => $info['options']]);
            }
            if (in_array('required', $this->rule ?? [], true)) {
                $value->required();
            }
        }
        if ($value instanceof Component) {
            $value = $this->callExt('formExt', $value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function buildDetail($value)
    {
        if ($value === true) {
            $value = DetailAttribute::make()
                ->name($this->key)
                ->label($this->label);
            if (($info = $this->getSelectOptionsInfo()) !== null) {
                $value->typeMapping(['map' => $info['map']]);
            }
        }
        if ($value instanceof Component) {
            $value = $this->callExt('detailExt', $value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function buildRule($value)
    {
        if (is_string($value)) {
            $value = array_values(array_filter(explode('|', $value)));
        }
        if ($this->scene === AbsRepository::SCENE_UPDATE) {
            // 为了保证 update 场景下，可能需要部分字段更新（quickEdit），此时给字段默认添加 sometimes 规则
            if (!in_array(self::SCENE_UPDATE_APPEND_RULE, $value, true)) {
                array_unshift($value, self::SCENE_UPDATE_APPEND_RULE);
            }
        }
        return $value;
    }

    /**
     * @var false|array|null
     */
    protected $selectOptionsInfo = false;

    protected function getSelectOptionsInfo(): ?array
    {
        if ($this->selectOptionsInfo !== false) {
            return $this->selectOptionsInfo;
        }
        $defineSelectOptions = value($this->getDefineValueByName('selectOptions'));
        if ($defineSelectOptions === null) {
            return $this->selectOptionsInfo = null;
        }

        $data = [
            'map' => [],
            'options' => [],
            'values' => [],
        ];
        if (!is_array($defineSelectOptions)) {
            throw new \InvalidArgumentException('selectOptions must be an array or Closure return array');
        }
        if (isset($defineSelectOptions[0]['value'])) {
            // 二维数组的形式
            $data['options'] = $defineSelectOptions;
            $data['map'] = array_column($defineSelectOptions, 'label', 'value');
        } else {
            // map 的形式
            $data['map'] = $defineSelectOptions;
            foreach ($defineSelectOptions as $value => $label) {
                $data['options'][] = [
                    'value' => (string)$value, // 强制为 string，保证行为一致
                    'label' => strip_tags($label), // 去除 html
                ];
            }
        }
        $data['values'] = array_keys($data['map']);

        return $this->selectOptionsInfo = $data;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function callExt(string $name, $value, bool $useScene = false)
    {
        $ext = $this->getDefineValueByName($name) ?? null;
        if ($ext) {
            if ($useScene) {
                $result = $ext($value, $this->scene);
            } else {
                $result = $ext($value);
            }
            if ($result !== null) {
                return $result;
            }
        }
        return $value;
    }
}
