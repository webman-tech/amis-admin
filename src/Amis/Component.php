<?php

namespace Kriss\WebmanAmisAdmin\Amis;

use support\Container;

/**
 * amis 组件
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/index
 */
class Component
{
    protected array $schema = [
        'type' => '',
    ];

    /**
     * @param array|null $schema
     * @return $this
     */
    public static function make(array $schema = null)
    {
        /** @var static $component */
        $component = Container::get(static::class);
        if ($schema) {
            $component->schema($schema);
        }
        return $component;
    }

    /**
     * @param array $schema
     * @param bool $overwrite
     * @return $this
     */
    public function schema(array $schema, bool $overwrite = false)
    {
        if ($overwrite) {
            $this->schema = $schema;
        } else {
            $this->schema = $this->merge($this->schema, $schema);
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->deepToArray($this->schema);
    }

    public function get(string $schemaKey, $default = null)
    {
        $keys = explode('.', $schemaKey);
        $array = $this->toArray();
        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }
        return $array;
    }

    protected function deepToArray(array $arr): array
    {
        $newArr = [];
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $item = $this->deepToArray($item);
            } elseif ($item instanceof Component) {
                $item = $item->toArray();
            }
            $newArr[$key] = $item;
        }
        return $newArr;
    }

    /**
     * @link https://github.com/yiisoft/arrays/blob/master/src/ArrayHelper.php::merge
     * @param ...$arrays
     * @return array
     */
    protected function merge(...$arrays): array
    {
        $result = array_shift($arrays) ?: [];
        while (!empty($arrays)) {
            /** @var mixed $value */
            foreach (array_shift($arrays) as $key => $value) {
                if (is_int($key)) {
                    if (array_key_exists($key, $result)) {
                        if ($result[$key] !== $value) {
                            /** @var mixed */
                            $result[] = $value;
                        }
                    } else {
                        /** @var mixed */
                        $result[$key] = $value;
                    }
                } elseif (isset($result[$key]) && is_array($value) && is_array($result[$key])) {
                    $result[$key] = $this->merge($result[$key], $value);
                } else {
                    /** @var mixed */
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}