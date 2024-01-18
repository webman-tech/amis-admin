<?php

namespace WebmanTech\AmisAdmin\Support;

final class ArrayHelper
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
}
