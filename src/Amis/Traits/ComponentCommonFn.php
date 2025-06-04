<?php

namespace WebmanTech\AmisAdmin\Amis\Traits;

/**
 * @internal
 */
trait ComponentCommonFn
{
    private function callToSetSchema(string $name, array $arguments)
    {
        $value = $arguments[0] ?? null;
        if ($value === null) {
            $value = $this->defaultValue[$name] ?? null;
        }
        if ($value === null) {
            unset($this->schema[$name]);
        } else {
            $this->schema[$name] = $value;
        }
    }

    /**
     * 处理 mapping 类型的 map
     * @return void
     */
    private function solveMappingMap()
    {
        if (isset($this->schema['map']) && !is_array($this->schema['map'][0] ?? null)) {
            // 将 [$value => $label] 强制转为 [{label: xx, value: xxx}] 的形式，可以防止 map 被转为 array 的情况
            $this->schema['map'] = array_map(
                fn($label, $value) => [
                    'label' => $label,
                    'value' => $value,
                ],
                array_values((array)$this->schema['map']),
                array_keys((array)$this->schema['map'])
            );
        }
    }
}