<?php

namespace WebmanTech\AmisAdmin\Helper\DTO;

use Closure;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

final class PresetItem
{
    public const SCENE_DEFAULT = 'default';
    private const RULE_SOMETIMES = 'sometimes';
    private const RULE_REQUIRED = 'required';
    private const RULE_NULLABLE = 'nullable';

    public function __construct(
        private readonly null|string|Closure       $label = null,
        private readonly null|string|Closure       $labelRemark = null,
        private readonly null|string|Closure       $description = null,
        private readonly null|bool|string|Closure  $filter = true,
        private readonly null|bool|Closure         $grid = true,
        private readonly null|Closure              $gridExt = null,
        private readonly null|Closure              $gridExtDynamic = null,
        private readonly null|bool|Closure         $form = true,
        private readonly null|Closure              $formExt = null,
        private readonly null|Closure              $formExtDynamic = null,
        private readonly null|bool|Closure         $detail = true,
        private readonly null|Closure              $detailExt = null,
        private readonly null|Closure              $detailExtDynamic = null,
        private readonly null|string|array|Closure $rule = self::RULE_NULLABLE, // 默认为 nullable，使得不填 rule 时可以正常提交和传递数据
        private readonly null|Closure              $ruleExtDynamic = null,
        private readonly null|array|Closure        $ruleMessages = null,
        private readonly null|string|Closure       $ruleCustomAttribute = null,
        private readonly null|array|Closure        $selectOptions = null,
        private readonly null|string|Closure       $formDefaultValue = null,
    )
    {
    }

    private string $key;

    public function withKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    private string $scene = self::SCENE_DEFAULT;

    public function withScene(?string $scene = null): self
    {
        $this->scene = $scene ?? self::SCENE_DEFAULT;
        return $this;
    }

    public function getScene(): string
    {
        return $this->scene;
    }

    public function getLabel(): ?string
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->label));
    }

    public function getLabelRemark(): ?string
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->labelRemark));
    }

    public function getDescription(): ?string
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->description));
    }

    public function getFilter(): ?Closure
    {
        return $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var bool|string|null|Closure $value */
            $value = value($this->filter);
            if ($value === null || $value === false) {
                return null;
            }

            if ($value === '=' || $value === true) {
                return fn($query, $value, $attribute) => $query->where($attribute, $value);
            }
            if ($value === 'like') {
                // TODO like 需要做转义
                return fn($query, $value, $attribute) => $query->where($attribute, 'like', '%' . $value . '%');
            }
            if ($value === 'datetime-range') {
                return fn($query, $value, $attribute) => $query
                    ->whereBetween($attribute, array_map(
                        fn($timestamp) => date('Y-m-d H:i:s', (int)$timestamp),
                        explode(',', (string)$value)
                    ));
            }

            // TODO 扩展其他 filter，或者方式（比如 static 直接注入？）


            if (is_string($value)) {
                throw new \InvalidArgumentException("filter value not support: {$value}");
            }
            return $value;
        });
    }

    /**
     * @return GridColumn[]|null
     */
    public function getGrid(): null|array
    {
        /** @var GridColumn[]|null $value */
        $value = $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var bool|null|array|GridColumn $value */
            $value = value($this->grid, $this->getKey());
            if ($value === false || $value === null) {
                return null;
            }
            if ($value === true) {
                $value = GridColumn::make()
                    ->name($this->getKey())
                    ->label($this->getLabel());
                // 自动添加选项 map
                if (($info = $this->getSelectOptions()) !== null) {
                    $value->typeMapping(['map' => $info['map']]);
                }
                // 自动添加搜索
                if ($this->getFilter() !== null) {
                    $value->searchable();
                }
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $k => $v) {
                if (!$v instanceof GridColumn) {
                    throw new \InvalidArgumentException('grid must be GridColumn instance or array with GridColumn instance');
                }
                if ($this->gridExt instanceof Closure) {
                    $v = call_user_func($this->gridExt, $v, $k);
                    if (!$v instanceof GridColumn) {
                        throw new \InvalidArgumentException('gridExt must be GridColumn instance');
                    }
                }
                $value[$k] = $v;
            }
            return $value;
        });

        if (is_array($value)) {
            if ($this->gridExtDynamic instanceof Closure) {
                foreach ($value as $k => $v) {
                    $v = call_user_func($this->gridExtDynamic, $v, $this->getScene(), $k);
                    if (!$v instanceof GridColumn) {
                        throw new \InvalidArgumentException('gridExtDynamic must be GridColumn instance');
                    }
                    $value[$k] = $v;
                }
            }
            $value = array_values($value);
        }

        return $value;
    }

    /**
     * @return FormField[]|null
     */
    public function getForm(): null|array
    {
        /** @var FormField[]|null $value */
        $value = $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var bool|null|FormField|array $value */
            $value = value($this->form, $this->getKey());
            if ($value === false || $value === null) {
                return null;
            }
            if ($value === true) {
                $value = FormField::make()
                    ->name($this->getKey())
                    ->label($this->getLabel())
                    ->labelRemark($this->getLabelRemark())
                    ->description($this->getDescription());
                // 自动添加选项
                if (($info = $this->getSelectOptions()) !== null) {
                    $value->typeSelect(['options' => $info['options']]);
                }
                // 没有动态 rule 时，在缓存中即自动添加 required
                if (!$this->hasRuleExtDynamic() && in_array(self::RULE_REQUIRED, $this->getRules() ?? [], true)) {
                    $value->required();
                }
                // 自动添加默认值
                if (($defaultValue = $this->getFormDefaultValue()) !== null) {
                    $value->value($defaultValue);
                }
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $k => $v) {
                if (!$v instanceof FormField) {
                    throw new \InvalidArgumentException('form must be FormField instance or array with FormField instance');
                }
                if ($this->formExt instanceof Closure) {
                    $v = call_user_func($this->formExt, $v, $k);
                    if (!$v instanceof FormField) {
                        throw new \InvalidArgumentException('formExt must be FormField instance');
                    }
                }
                $value[$k] = $v;
            }
            return $value;
        });

        if (is_array($value)) {
            if ($this->formExtDynamic instanceof Closure) {
                foreach ($value as $k => $v) {
                    $v = call_user_func($this->formExtDynamic, $v, $this->getScene(), $k);
                    if (!$v instanceof FormField) {
                        throw new \InvalidArgumentException('formExtDynamic must be FormField instance');
                    }
                    $value[$k] = $v;
                }
            }

            // 有动态的 rule 规则时，再重新处理一遍 required
            if ($this->hasRuleExtDynamic() && $rules = $this->getRules() ?? []) {
                foreach ($value as $v) {
                    $isRequired = in_array(self::RULE_REQUIRED, $rules, true);
                    if ($isRequired) {
                        $v->required();
                    } else {
                        if ($v->get('required')) {
                            $v->required(false);
                        }
                    }
                }
            }

            $value = array_values($value);
        }

        return $value;
    }

    /**
     * @return DetailAttribute[]|null
     */
    public function getDetail(): null|array
    {
        /** @var DetailAttribute[]|null $value */
        $value = $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var bool|null|array|DetailAttribute $value */
            $value = value($this->detail, $this->getKey());
            if ($value === false || $value === null) {
                return null;
            }
            if ($value === true) {
                $value = DetailAttribute::make()
                    ->name($this->getKey())
                    ->label($this->getLabel());
                // 自动添加选项 map
                if (($info = $this->getSelectOptions()) !== null) {
                    $value->typeMapping(['map' => $info['map']]);
                }
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $k => $v) {
                if (!$v instanceof DetailAttribute) {
                    throw new \InvalidArgumentException('form must be DetailAttribute instance or array with DetailAttribute instance');
                }
                if ($this->detailExt instanceof Closure) {
                    $v = call_user_func($this->detailExt, $v, $k);
                    if (!$v instanceof DetailAttribute) {
                        throw new \InvalidArgumentException('detailExt must be DetailAttribute instance');
                    }
                }
                $value[$k] = $v;
            }
            return $value;
        });

        if (is_array($value)) {
            if ($this->detailExtDynamic instanceof Closure) {
                foreach ($value as $k => $v) {
                    $v = call_user_func($this->detailExtDynamic, $v, $this->getScene(), $k);
                    if (!$v instanceof DetailAttribute) {
                        throw new \InvalidArgumentException('detailExtDynamic must be DetailAttribute instance');
                    }
                    $value[$k] = $v;
                }
            }
            $value = array_values($value);
        }

        return $value;
    }

    private function hasRuleExtDynamic(): bool
    {
        return $this->ruleExtDynamic instanceof Closure;
    }

    public function getRules(): ?array
    {
        /** @var array|null $value */
        $value = $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var bool|null|string|array $value */
            $value = value($this->rule);
            if ($value === false || $value === null) {
                return null;
            }
            // 将字符串形式 rule 切割成数组
            if (is_string($value)) {
                $value = array_values(array_filter(explode('|', $value)));
            }
            return $value;
        });

        if ($this->ruleExtDynamic instanceof Closure) {
            $value = call_user_func($this->ruleExtDynamic, $value, $this->getScene());
        }
        // 将字符串形式 rule 切割成数组
        if (is_string($value)) {
            $value = array_values(array_filter(explode('|', $value)));
        }

        // 更新时默认添加 sometimes 规则
        if ($this->getScene() === RepositoryInterface::SCENE_UPDATE && is_array($value)) {
            // 为了保证 update 场景下，可能需要部分字段更新（quickEdit），此时给字段默认添加 sometimes 规则
            if (!in_array(self::RULE_SOMETIMES, $value, true)) {
                array_unshift($value, self::RULE_SOMETIMES);
            }
        }

        return $value;
    }

    public function getRuleMessages(): ?array
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->ruleMessages));
    }

    public function getRuleCustomAttribute(): mixed
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->ruleCustomAttribute));
    }

    /**
     * @return array{map: array, options: array, values: array}|null
     */
    private function getSelectOptions(): ?array
    {
        return $this->getOrSetCacheValue(__FUNCTION__, function () {
            /** @var null|array|mixed $value */
            $value = value($this->selectOptions);
            if ($value === null) {
                return null;
            }
            if (!is_array($value)) {
                throw new \InvalidArgumentException('selectOptions must be an array or Closure return array');
            }
            $data = [];
            /** @phpstan-ignore-next-line */
            if (isset($value[0]['value'])) {
                // 二维数组的形式
                $data['options'] = $value;
                $data['map'] = array_column($value, 'label', 'value');
            } else {
                // map 的形式
                $data['map'] = $value;
                foreach ($value as $val => $label) {
                    $data['options'][] = [
                        'value' => (string)$val, // 强制为 string，保证行为一致
                        'label' => strip_tags((string)$label), // 去除 html
                    ];
                }
            }
            $data['values'] = array_keys($data['map']);

            return array_merge([
                'map' => [],
                'options' => [],
                'values' => [],
            ], $data);
        });
    }

    private function getFormDefaultValue(): ?string
    {
        return $this->getOrSetCacheValue(__FUNCTION__, fn() => value($this->formDefaultValue));
    }

    private array $cache = [];

    private function getOrSetCacheValue(string $key, Closure $value): mixed
    {
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $value() ?? '__NULL__';
        }
        return $this->cache[$key] === '__NULL__' ? null : $this->cache[$key];
    }
}
