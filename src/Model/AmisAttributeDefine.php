<?php

namespace WebmanTech\AmisAdmin\Model;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;

/**
 * @method null|string|array<string, string> pickLabel(?string $attribute = null)
 * @method null|string|array<string, string> pickLabelRemark(?string $attribute = null)
 * @method null|string|array<string, string> pickDescription(?string $attribute = null)
 * @method null|Closure|array<string, Closure> pickFilter(?string $attribute = null)
 * @method null|GridColumn|array<string, GridColumn> pickGrid(?string $attribute = null)
 * @method null|FormField|array<string, FormField> pickForm(?string $attribute = null)
 * @method null|FormField|array<string, FormField> pickRule(?string $attribute = null)
 * @method null|FormField|array<string, FormField> pickRuleMessages(?string $attribute = null)
 */
class AmisAttributeDefine
{
    protected Model $model;
    protected array $define = [];

    public function withDefine(array $define)
    {
        $this->define = $define;
        return $this;
    }

    protected ?string $scene = null;

    public function withScene(?string $scene)
    {
        $this->scene = $scene;
        return $this;
    }

    protected array $items = [];

    /**
     * @param string|null $attribute
     * @return array<string, AmisAttributeDefineItem>|AmisAttributeDefineItem
     */
    public function getItem(?string $attribute = null)
    {
        if ($attribute === null) {
            $data = [];
            foreach (array_keys($this->define) as $attribute) {
                $data[] = $this->getItem($attribute);
            }
            return $data;
        }

        if (!isset($this->items[$attribute])) {
            if (!isset($this->define[$attribute])) {
                throw new \Exception("Attribute {$attribute} not defined");
            }
            $this->items[$attribute] = new AmisAttributeDefineItem($attribute, $this->define[$attribute]);
        }

        return $this->items[$attribute];
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'pick')) {
            $key = lcfirst(substr($name, 4));
            $attribute = $arguments[0] ?? null;
            if ($attribute === null) {
                $data = [];
                foreach ($this->getItem() as $attribute => $item) {
                    if ($value = $item->withScene($this->scene)->{$key}) {
                        $data[$attribute] = $value;
                    }
                }
                return $data;
            }
            return $this->getItem($attribute)->withScene($this->scene)->{$key};
        }

        throw new \Exception("Method {$name} not found");
    }
}