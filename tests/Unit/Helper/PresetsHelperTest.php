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

beforeEach(function () {
    $this->presetsHelper = new PresetsHelper();
});

test('support withPresets', function () {
    $presetsHelper = $this->presetsHelper;
    expect(array_keys($presetsHelper->pickLabel()))->toBe([]);

    $presetsHelper->withPresets([
        'id' => [
            'label' => 'ID',
        ],
    ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID']);
});

test('support withDefaultNoEdit', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'before' => [],
        ])
        ->withDefaultNoEdit() // 该配置之前的定义不受影响
        ->withPresets([
            'after' => [],
        ]);
    expect($presetsHelper->pickForm())->toHaveCount(1)
        ->and($presetsHelper->pickForm()[0]->get('name'))->toBe('before')
        ->and($presetsHelper->pickGrid())->toHaveCount(2)
        ->and($presetsHelper->pickDetail())->toHaveCount(2);
});

test('support withDefaultSceneKeys', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Code']
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code']);
    $presetsHelper->withDefaultSceneKeys(['id']);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID']);
});

test('support withCrudSceneKeys', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Code']
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code']);
    $presetsHelper->withCrudSceneKeys(['id']);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code'])
        ->and($presetsHelper->withScene(AbsRepository::SCENE_CREATE)->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->withScene(AbsRepository::SCENE_CREATE)->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->withScene(AbsRepository::SCENE_UPDATE)->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->withScene(AbsRepository::SCENE_DETAIL)->pickLabel())->toBe(['id' => 'ID']);
});

test('support withSceneKeys', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Code']
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code']);
    $presetsHelper->withSceneKeys([
        'scene_abc' => ['id'],
        'scene_xyz' => ['code'],
    ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code'])
        ->and($presetsHelper->withScene('scene_abc')->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->withScene('scene_xyz')->pickLabel())->toBe(['code' => 'Code']);
});

test('support withScene', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Code']
        ])
        ->withSceneKeys([
            'scene_abc' => ['id']
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code'])
        ->and($presetsHelper->withScene('scene_abc')->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->pickLabel())->toBe(['id' => 'ID']) // withScene 具有副作用，会更改全局当前的 scene
        ->and($presetsHelper->withScene()->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code']); // 使用 withScene(null) 重置 scene
});

test('support label', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => [
                'label' => 'ID',
            ],
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID'])
        ->and($presetsHelper->pickGrid(['id'])[0]->get('label'))->toBe('ID')
        ->and($presetsHelper->pickForm(['id'])[0]->get('label'))->toBe('ID')
        ->and($presetsHelper->pickDetail(['id'])[0]->get('label'))->toBe('ID');
});

test('support labelRemark', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => [
                'labelRemark' => 'ID',
            ],
        ]);
    expect($presetsHelper->pickLabelRemark())->toBe(['id' => 'ID'])
        ->and($presetsHelper->pickGrid(['id'])[0]->get('labelRemark'))->toBeNull()
        ->and($presetsHelper->pickForm(['id'])[0]->get('labelRemark'))->toBe('ID')
        ->and($presetsHelper->pickDetail(['id'])[0]->get('labelRemark'))->toBeNull();
});

test('support description', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => [
                'description' => 'ID',
            ],
        ]);
    expect($presetsHelper->pickDescription())->toBe(['id' => 'ID'])
        ->and($presetsHelper->pickGrid(['id'])[0]->get('description'))->toBeNull()
        ->and($presetsHelper->pickForm(['id'])[0]->get('description'))->toBe('ID')
        ->and($presetsHelper->pickDetail(['id'])[0]->get('description'))->toBeNull();
});

test('support filter', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'key0' => [
            ],
            'key1' => [
                'filter' => true,
            ],
            'key2' => [
                'filter' => '=',
            ],
            'key3' => [
                'filter' => 'like',
            ],
            'key4' => [
                'filter' => null,
            ],
        ]);
    $filters = $presetsHelper->pickFilter();
    expect($filters['key0'])->toBeInstanceOf(Closure::class)
        ->and($filters['key1'])->toBeInstanceOf(Closure::class)
        ->and($filters['key2'])->toBeInstanceOf(Closure::class)
        ->and($filters['key3'])->toBeInstanceOf(Closure::class)
        ->and(array_key_exists('key4', $filters))->toBeFalse();
});

test('support grid', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default' => [
            ],
            'change_grid' => [
                'grid' => fn(string $key) => GridColumn::make()->name($key),
            ],
            'ext_grid' => [
                'gridExt' => fn(GridColumn $column) => $column->sortable(),
            ],
            'no_filter' => [
                'filter' => null,
            ],
            'hidden' => [
                'grid' => null,
            ],
        ]);
    expect(components_to_array($presetsHelper->pickGrid()))->toBe(components_to_array([
        GridColumn::make()->name('default')->searchable(),
        GridColumn::make()->name('change_grid'),
        GridColumn::make()->name('ext_grid')->searchable()->sortable(),
        GridColumn::make()->name('no_filter'),
    ]));
});

test('support form', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default' => [
            ],
            'change_form' => [
                'form' => fn(string $key) => FormField::make()->name($key),
            ],
            'ext_form' => [
                'formExt' => fn(FormField $field) => $field->hidden(),
            ],
            'auto_required' => [
                'rule' => 'required',
            ],
            'hidden' => [
                'form' => null,
            ],
        ]);
    expect(components_to_array($presetsHelper->pickForm()))->toBe(components_to_array([
        FormField::make()->name('default'),
        FormField::make()->name('change_form'),
        FormField::make()->name('ext_form')->hidden(),
        FormField::make()->name('auto_required')->required(),
    ]));
});

test('support detail', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default' => [
            ],
            'change_detail' => [
                'detail' => fn(string $key) => DetailAttribute::make()->name($key),
            ],
            'ext_detail' => [
                'detailExt' => fn(DetailAttribute $attribute) => $attribute->typeImage(),
            ],
            'hidden' => [
                'detail' => null,
            ],
        ]);
    expect(components_to_array($presetsHelper->pickDetail()))->toBe(components_to_array([
        DetailAttribute::make()->name('default'),
        DetailAttribute::make()->name('change_detail'),
        DetailAttribute::make()->name('ext_detail')->typeImage(),
    ]));
});

test('support rule', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default' => [
            ],
            'change_rule' => [
                'rule' => 'required',
            ],
            'change_rule2' => [
                'rule' => 'required|string',
            ],
            'callback_rule' => [
                'rule' => fn() => 'required',
            ],
            'hidden' => [
                'rule' => null,
            ],
        ]);
    expect($presetsHelper->pickRules())->toBe([
        'default' => ['nullable'],
        'change_rule' => ['required'],
        'change_rule2' => ['required', 'string'],
        'callback_rule' => ['required'],
    ]);
});

test('support ruleMessages', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default_hidden' => [
            ],
            'change_ruleMessages' => [
                'ruleMessages' => ['required' => 'abc'],
            ],
            'callback_ruleMessages' => [
                'ruleMessages' => fn() => ['required' => 'abc'],
            ],
        ]);
    expect($presetsHelper->pickRuleMessages())->toBe([
        'change_ruleMessages' => ['required' => 'abc'],
        'callback_ruleMessages' => ['required' => 'abc'],
    ]);
});

test('support ruleCustomAttribute', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'default_hidden' => [
            ],
            'change_ruleCustomAttribute' => [
                'ruleCustomAttribute' => 'abc',
            ],
            'callback_ruleCustomAttribute' => [
                'ruleCustomAttribute' => fn() => 'abc',
            ],
        ]);
    expect($presetsHelper->pickRuleCustomAttributes())->toBe([
        'change_ruleCustomAttribute' => 'abc',
        'callback_ruleCustomAttribute' => 'abc',
    ]);
});

test('support selectOptions', function () {
    $presetsHelper = $this->presetsHelper->withPresets([
        'id' => [
            'selectOptions' => ['a' => 'A', 'b' => 'B'],
        ],
    ]);
    expect(components_to_array($presetsHelper->pickGrid()))
        ->toBe(components_to_array([
            GridColumn::make()->name('id')->typeMapping(['map' => [
                ['label' => 'A', 'value' => 'a'],
                ['label' => 'B', 'value' => 'b'],
            ]])->searchable()
        ]))
        ->and(components_to_array($presetsHelper->pickForm()))
        ->toBe(components_to_array([
            FormField::make()->name('id')->typeSelect(['options' => [
                ['value' => 'a', 'label' => 'A'],
                ['value' => 'b', 'label' => 'B'],
            ]]),
        ]))
        ->and(components_to_array($presetsHelper->pickDetail()))
        ->toBe(components_to_array([
            DetailAttribute::make()->name('id')->typeMapping(['map' => [
                ['label' => 'A', 'value' => 'a'],
                ['label' => 'B', 'value' => 'b'],
            ]]),
        ]));
});

test('support pick special keys', function () {
    $presetsHelper = $this->presetsHelper
        ->withPresets([
            'id' => [
                'label' => 'ID',
            ],
            'code' => [
                'label' => 'Code',
            ]
        ]);
    expect($presetsHelper->pickLabel())->toBe(['id' => 'ID', 'code' => 'Code'])
        ->and($presetsHelper->pickLabel(['id']))->toBe(['id' => 'ID']);
});

test('support pickForm multi field', function () {
    $presetsHelper = $this->presetsHelper->withPresets([
        'id' => [
            'form' => fn(string $column) => FormField::make()->name($column)
        ],
        'code' => [
            'form' => fn(string $column) => [
                FormField::make()->name($column . '1'),
                FormField::make()->name($column . '2'),
            ],
        ]
    ]);
    expect(components_to_array($presetsHelper->pickForm()))->toBe(components_to_array([
        FormField::make()->name('id'),
        FormField::make()->name('code1'),
        FormField::make()->name('code2'),
    ]));
});

test('support extDynamic', function () {
    $presetsHelper = $this->presetsHelper->withPresets([
        'key_useDynamic' => [
            'formExtDynamic' => fn(FormField $field, string $scene) => $field->required($scene === 'create'),
            'ruleExtDynamic' => fn(array $rule, string $scene) => array_values(array_filter([
                $scene === 'create' ? 'required' : null,
                'string',
            ])),
        ],
    ]);

    expect($presetsHelper->withScene()->pickForm()[0]->get('required'))->toBeFalse()
        ->and($presetsHelper->withScene('create')->pickForm()[0]->get('required'))->toBeTrue()
        ->and($presetsHelper->withScene()->pickRules()['key_useDynamic'])->toBe(['string'])
        ->and($presetsHelper->withScene('create')->pickRules()['key_useDynamic'])->toBe(['required', 'string']);
});

test('ext and extDynamic compare', function () {
    $globalValue = new stdClass();
    $globalValue->value = '123';
    $presetsHelper = $this->presetsHelper->withPresets([
        'key_noDynamic' => [
            'gridExt' => fn(GridColumn $column) => $column->width($globalValue->value),
            'formExt' => fn(FormField $field) => $field->value($globalValue->value),
            'detailExt' => fn(DetailAttribute $attribute) => $attribute->value($globalValue->value),
        ],
        'key_useDynamic' => [
            'gridExtDynamic' => fn(GridColumn $column) => $column->width($globalValue->value),
            'formExtDynamic' => fn(FormField $field, string $scene) => $field->value($globalValue->value),
            'detailExtDynamic' => fn(DetailAttribute $attribute, string $scene) => $attribute->value($globalValue->value),
        ],
    ]);
    $grids = $presetsHelper->pickGrid();
    $forms = $presetsHelper->pickForm();
    $details = $presetsHelper->pickDetail();
    expect($grids[0]->get('width'))->toBe('123')
        ->and($forms[0]->get('value'))->toBe('123')
        ->and($details[0]->get('value'))->toBe('123')
        ->and($grids[1]->get('width'))->toBe('123')
        ->and($forms[1]->get('value'))->toBe('123')
        ->and($details[1]->get('value'))->toBe('123');

    $globalValue->value = '456'; // 修改值，仅 ExtDynamic 的才会变
    $grids = $presetsHelper->pickGrid();
    $forms = $presetsHelper->pickForm();
    $details = $presetsHelper->pickDetail();
    expect($grids[0]->get('width'))->toBe('123')
        ->and($forms[0]->get('value'))->toBe('123')
        ->and($details[0]->get('value'))->toBe('123')
        ->and($grids[1]->get('width'))->toBe('456')
        ->and($forms[1]->get('value'))->toBe('456')
        ->and($details[1]->get('value'))->toBe('456');
});