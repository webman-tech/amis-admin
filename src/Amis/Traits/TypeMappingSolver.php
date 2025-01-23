<?php

namespace WebmanTech\AmisAdmin\Amis\Traits;

/**
 * @internal
 */
trait TypeMappingSolver
{
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