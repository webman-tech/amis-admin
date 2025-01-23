<?php

use WebmanTech\AmisAdmin\Helper\ArrayHelper;

test('merge', function () {
    // 不同 key 合并
    expect(ArrayHelper::merge(
        ['a' => 1],
        ['b' => 2],
    ))
        ->toBe([
            'a' => 1,
            'b' => 2,
        ])
        // 同 key 合并
        ->and(ArrayHelper::merge(
            ['a' => 1],
            ['a' => 2],
        ))
        ->toBe([
            'a' => 2,
        ])
        // 多维数组合并
        ->and(ArrayHelper::merge(
            ['a' => ['x' => 'y']],
            ['a' => ['m' => 'n']],
        ))
        ->toBe([
            'a' => [
                'x' => 'y',
                'm' => 'n',
            ],
        ])
        // 多维 indexed 数组合并
        ->and(ArrayHelper::merge(
            ['a' => ['x', 'y']],
            ['a' => ['m', 'n']],
        ))
        ->toBe([
            'a' => ['x', 'y', 'm', 'n'],
        ])
        ;
});