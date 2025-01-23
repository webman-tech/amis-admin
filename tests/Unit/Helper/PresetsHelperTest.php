<?php

use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

function components_to_array(array $items): array
{
    return array_map(fn(Component $item) => $item->toArray(), $items);
}

function components_to_json(array $items): string
{
    return json_encode(components_to_array($items));
}

test('simple', function () {
    $presetsHelper = new PresetsHelper([
        'id' => [
            'label' => 'ID',
            'labelRemark' => 'ID remark',
            'description' => 'ID description',
            'filter' => '=',
            'grid' => fn(string $column) => GridColumn::make()->name($column)->searchable(),
            'form' => null,
            'detail' => true,
        ],
        'code' => [
            'label' => '编码',
            'filter' => '=',
            'grid' => fn(string $column) => GridColumn::make()->name($column)->searchable(),
            'form' => fn(string $column, string $scene) => FormField::make()->name($column)->required($scene === AbsRepository::SCENE_CREATE),
            'detail' => true,
            'rule' => 'required|string|max:64',
        ],
    ]);

    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => '编码'])
        ->and($presetsHelper->pickLabelRemark())->toBe(['id' => 'ID remark'])
        ->and($presetsHelper->pickDescription())->toBe(['id' => 'ID description'])
        ->and(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
            GridColumn::make()->name('id')->searchable(),
            GridColumn::make()->name('code')->searchable()
        ]))
        ->and(array_keys($presetsHelper->pickFilter()))->toBe(['id', 'code']) // 无法比较匿名函数
        ->and(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))->toBe(components_to_array([
            FormField::make()->name('code')->required()
        ]))
        ->and(components_to_array($presetsHelper->pickDetail()))->toBe(components_to_array([
            DetailAttribute::make()->name('id'),
            DetailAttribute::make()->name('code'),
        ]))
        ->and($presetsHelper->pickRules(AbsRepository::SCENE_CREATE))->toBe([
            'id' => 'nullable',
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
                $scene === AbsRepository::SCENE_CREATE ? 'required' : null,
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
        ->and(components_to_array($presetsHelper->pickDetail()))->toBe(components_to_array([
            DetailAttribute::make()->name('id'),
            DetailAttribute::make()->name('code'),
            DetailAttribute::make()->name('code2'),
        ]))
        ->and($presetsHelper->pickRules(AbsRepository::SCENE_CREATE))->toBe([
            'id' => 'nullable',
            'code' => 'required|string|max:64',
            'code2' => ['required', 'string'],
        ])
        ->and($presetsHelper->pickRules(AbsRepository::SCENE_UPDATE))->toBe([
            'id' => 'nullable',
            'code' => 'required|string|max:64',
            'code2' => ['string'],
        ])
        ->and(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))->toBe(components_to_array([
            FormField::make()->name('id'),
            FormField::make()->name('code')->required(),
            FormField::make()->name('code2')->required(),
        ]))
        ->and(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_UPDATE)))->toBe(components_to_array([
            FormField::make()->name('id'),
            FormField::make()->name('code')->required(),
            FormField::make()->name('code2'),
        ]));
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

test('check withDefaultNoEdit', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
        ]
    ]))->withDefaultNoEdit();
    expect(array_keys($presetsHelper->pickLabel()))->toBe(['id', 'code'])
        ->and($presetsHelper->pickForm(AbsRepository::SCENE_CREATE))->toBe([])
        ->and($presetsHelper->pickForm(AbsRepository::SCENE_UPDATE))->toBe([])
        ->and(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
            GridColumn::make()->name('id')->searchable(),
            GridColumn::make()->name('code')->searchable(),
        ]))
        ->and(components_to_array($presetsHelper->pickDetail()))->toBe(components_to_array([
            DetailAttribute::make()->name('id'),
            DetailAttribute::make()->name('code'),
        ]));
});

test('check withCrudSceneKeys', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
        ]
    ]))->withCrudSceneKeys(['id']);
    expect(array_keys($presetsHelper->pickLabel()))->toBe(['id', 'code'])
        ->and(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))->toBe(components_to_array([
            FormField::make()->name('id'),
        ]));
});

test('check withSceneKeys', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
        ]
    ]))->withSceneKeys([
        'abcScene' => ['code']
    ]);
    expect(array_keys($presetsHelper->pickLabel()))->toBe(['id', 'code'])
        ->and(components_to_array($presetsHelper->pickForm('abcScene')))->toBe(components_to_array([
            FormField::make()->name('code'),
        ]));
});

test('support pick special keys', function () {
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

test('pickForm support multi field', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'form' => fn(string $column, string $scene) => FormField::make()->name($column)
        ],
        'code' => [
            'form' => fn(string $column, string $scene) => [
                FormField::make()->name($column . '1'),
                FormField::make()->name($column . '2'),
            ],
        ]
    ]));
    expect(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))->toBe(components_to_array([
        FormField::make()->name('id'),
        FormField::make()->name('code1'),
        FormField::make()->name('code2'),
    ]));
});

test('pickForm required auto support', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'form' => true,
            'rule' => 'required',
        ],
        'code' => [
            'form' => true,
        ],
    ]));
    expect(components_to_array($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))->toBe(components_to_array([
        FormField::make()->name('id')->required(),
        FormField::make()->name('code'),
    ]));
});

test('pickGrid searchable auto support', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'grid' => true,
        ],
        'code' => [
            'grid' => true,
        ],
    ]));
    expect(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
        GridColumn::make()->name('id')->searchable(),
        GridColumn::make()->name('code')->searchable(),
    ]));
});

test('selectOptions support', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
        ],
        'code' => [
            'label' => '编码',
            'filter' => null,
        ],
    ]));
    expect(components_to_array($presetsHelper->pickGrid()))
        ->toBe(components_to_array([
            GridColumn::make()->name('id')->searchable(),
            GridColumn::make()->name('code'),
        ]));
});

test('gridExt formExt detailExt support', function () {
    $presetsHelper = (new PresetsHelper([
        'id' => [
            'label' => 'ID',
            'gridExt' => fn(GridColumn $column) => $column->sortable(),
            'formExt' => fn(FormField $field) => $field->typeInputNumber(),
            'detailExt' => fn(DetailAttribute $attribute) => $attribute->typeImage(),
        ],
        'code' => [
            'label' => '编码',
            'gridExt' => fn(GridColumn $column) => $column->sortable(),
            'formExt' => fn(FormField $field) => $field->typeInputNumber(),
            'detailExt' => fn(DetailAttribute $attribute) => $attribute->typeImage(),
        ],
    ]));

    expect(components_to_json($presetsHelper->pickGrid()))
        ->toBe(components_to_json([
            GridColumn::make()->name('id')->searchable()->sortable(),
            GridColumn::make()->name('code')->searchable()->sortable(),
        ]))
        ->and(components_to_json($presetsHelper->pickForm(AbsRepository::SCENE_CREATE)))
        ->toBe(components_to_json([
            FormField::make()->name('id')->typeInputNumber(),
            FormField::make()->name('code')->typeInputNumber(),
        ]))
        ->and(components_to_json($presetsHelper->pickDetail()))
        ->toBe(components_to_json([
            DetailAttribute::make()->name('id')->typeImage(),
            DetailAttribute::make()->name('code')->typeImage(),
        ]));
});