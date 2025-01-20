<?php

use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Controller\AmisSourceController;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;

function components_to_array(array $items): array
{
    return array_map(fn(Component $item) => $item->toArray(), $items);
}

test('simple', function () {
    $presetsHelper = new PresetsHelper([
        'id' => [
            'label' => 'ID',
            'filter' => '=',
            'grid' => fn(string $column) => GridColumn::make()->name($column)->searchable(),
            'form' => null,
            'detail' => true,
        ],
        'code' => [
            'label' => '编码',
            'filter' => '=',
            'grid' => fn(string $column) => GridColumn::make()->name($column)->searchable(),
            'form' => fn(string $column, string $scene) => FormField::make()->name($column)->required($scene === AmisSourceController::SCENE_CREATE),
            'detail' => true,
            'rule' => 'required|string|max:64',
        ],
    ]);

    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => '编码'])
        ->and(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
            GridColumn::make()->name('id')->searchable(),
            GridColumn::make()->name('code')->searchable()
        ]))
        ->and(array_keys($presetsHelper->pickFilter()))->toBe(['id', 'code']) // 无法比较匿名函数
        ->and(components_to_array($presetsHelper->pickForm(AmisSourceController::SCENE_CREATE)))->toBe(components_to_array([
            FormField::make()->name('code')->required(true)
        ]))
        ->and($presetsHelper->pickDetail())->toBe([
            'id',
            'code',
        ])
        ->and($presetsHelper->pickRules(AmisSourceController::SCENE_CREATE))->toBe([
            'code' => 'required|string|max:64',
        ]);
});

test('default', function () {
    $presetsHelper = new PresetsHelper([
        'id' => [],
        'code' => [
            'rule' => 'required|string|max:64',
        ],
        'code2' => [
            'filter' => null,
            'rule' => fn(string $scene) => array_values(array_filter([
                $scene === AmisSourceController::SCENE_CREATE ? 'required' : null,
                'string'
            ])),
        ]
    ]);

    expect($presetsHelper->pickLabel())->toBe([])
        ->and(array_keys($presetsHelper->pickFilter()))->toBe(['id', 'code']) // 无法比较匿名函数
        ->and(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
            GridColumn::make()->name('id')->searchable(),
            GridColumn::make()->name('code')->searchable(),
            GridColumn::make()->name('code2'),
        ]))
        ->and($presetsHelper->pickDetail())->toBe([
            'id',
            'code',
            'code2',
        ])
        ->and($presetsHelper->pickRules(AmisSourceController::SCENE_CREATE))->toBe([
            'code' => 'required|string|max:64',
            'code2' => ['required', 'string'],
        ])
        ->and($presetsHelper->pickRules(AmisSourceController::SCENE_UPDATE))->toBe([
            'code' => 'required|string|max:64',
            'code2' => ['string'],
        ])
        ->and(components_to_array($presetsHelper->pickForm(AmisSourceController::SCENE_CREATE)))->toBe(components_to_array([
            FormField::make()->name('id'),
            FormField::make()->name('code')->required(),
            FormField::make()->name('code2')->required(),
        ]))
        ->and(components_to_array($presetsHelper->pickForm(AmisSourceController::SCENE_UPDATE)))->toBe(components_to_array([
            FormField::make()->name('id'),
            FormField::make()->name('code')->required(),
            FormField::make()->name('code2'),
        ]))
        ;
});

test('check withPresets', function () {
    $presetsHelper = new PresetsHelper();
    expect(array_keys($presetsHelper->pickLabel()))->toBe([]);

    $presetsHelper->withPresets([
        'id' => [
            'label' => 'ID',
        ],
    ]);
    expect($presetsHelper->pickLabel())->toBe([]); // 后更改的无效

    $presetsHelper = (new PresetsHelper())
        ->withPresets([
            'id' => [
                'label' => 'ID',
            ],
        ]);
    expect(array_keys($presetsHelper->pickLabel()))->toBe(['id']);
});

test('check withDefaultEnable', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
        ]
    ]))->withDefaultEnable(['id']);
    expect(array_keys($presetsHelper->pickLabel()))->toBe(['id']);
});

test('支持指定 key', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
        ]
    ]));
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => '编码'])
        ->and($presetsHelper->pickLabel(['id']))->toBe(['id' => 'ID']);
});

test('pickForm 支持多个 field', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'form' => fn(string $column, string $scene) => FormField::make()->name($column)
        ],
        'code' => [
            'form' => fn(string $column, string $scene) => [
                FormField::make()->name($column.'1'),
                FormField::make()->name($column.'2'),
            ],
        ]
    ]));
    expect(components_to_array($presetsHelper->pickForm(AmisSourceController::SCENE_CREATE)))->toBe(components_to_array([
        FormField::make()->name('id'),
        FormField::make()->name('code1'),
        FormField::make()->name('code2'),
    ]));
});