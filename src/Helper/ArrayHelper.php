<?php

namespace Kriss\WebmanAmisAdmin\Helper;

class ArrayHelper
{
    /**
     * 合并数组
     * @link https://github.com/yiisoft/arrays/blob/master/src/ArrayHelper.php::merge
     * @param ...$arrays
     * @return array
     */
    public static function merge(...$arrays): array
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
                    $result[$key] = static::merge($result[$key], $value);
                } else {
                    /** @var mixed */
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * 获取数组中的值
     * @param array $array
     * @param string|int|null $key
     * @param mixed $default
     * @return mixed
     * @see \Illuminate\Support\Arr::get()
     */
    public static function get(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}