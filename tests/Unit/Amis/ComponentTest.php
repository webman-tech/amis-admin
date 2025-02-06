<?php

use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;

beforeEach(function () {
    ConfigHelper::$isForTest = true;
    ConfigHelper::reset();

    // 清空容器，否则会影响并行测试时修改 components.Xxx 的配置
    /** @var \Tests\Fixtures\ClearableContainer $container */
    $container = \support\Container::instance();
    $container->clear();
});

test('toArray simple', function () {
    $component = new Component();
    $component->schema([
        'type' => 'text',
        'body' => 'hello',
        'body_object' => [
            'type' => 'text',
        ],
        'body_array' => [
            [
                'type' => 'text',
            ]
        ],
    ]);
    expect($component->toArray())->toBe([
        'type' => 'text',
        'body' => 'hello',
        'body_object' => [
            'type' => 'text',
        ],
        'body_array' => [
            [
                'type' => 'text',
            ]
        ],
    ]);
});

test('toArray with deep', function () {
    $component = new Component();
    $component->schema([
        'type' => 'text',
    ]);

    // deep toArray
    $component2 = new Component();
    $component2->schema([
        'type' => 'text',
        'body' => [
            $component,
        ]
    ]);
    expect($component2->toArray())->toBe([
        'type' => 'text',
        'body' => [
            [
                'type' => 'text',
            ]
        ]
    ]);

    // deep toArray2
    $component2 = new Component();
    $component2->schema([
        'type' => 'text',
        'body' => $component,
    ]);
    expect($component2->toArray())->toBe([
        'type' => 'text',
        'body' => [
            'type' => 'text',
        ]
    ]);
});

test('make with schema', function () {
    $component = Component::make([
        'type' => 'text',
    ]);
    expect($component->toArray())->toBe([
        'type' => 'text',
    ]);
});

test('make with no config', function () {
    $component = Component::make();
    expect($component->toArray())->toBe([
        'type' => '',
    ]);
});

test('make with config', function () {
    ConfigHelper::$testConfig = [
        'components.' . Component::class => [
            'schema' => [
                'value' => '123',
            ],
        ],
    ];

    $component = Component::make();
    expect($component->toArray())->toBe([
        'type' => '',
        'value' => '123',
    ]);
});

test('toArray with components config', function () {
    ConfigHelper::$testConfig = [
        'components.typeInputText' => [
            'schema' => [
                'clearable' => true,
            ],
        ],
    ];

    $component = Component::make([
        'type' => 'input-text',
    ]);
    expect($component->toArray())->toBe([
        'clearable' => true,
        'type' => 'input-text',
    ]);

    $component = FormField::make()->typeInputText();
    expect($component->toArray())->toBe([
        'clearable' => true,
        'type' => 'input-text',
        'name' => '',
    ]);
});

test('toArray with components config, deep', function () {
    ConfigHelper::$testConfig = [
        'components.typeText' => [
            'schema' => [
                'level' => 'primary',
            ],
        ],
    ];

    $component = Component::make([
        'type' => 'tpl',
        'body' => [
            'type' => 'text',
        ]
    ]);
    expect($component->toArray())->toBe([
        'type' => 'tpl',
        'body' => [
            'level' => 'primary',
            'type' => 'text',
        ]
    ]);

    $component = Component::make([
        'type' => 'tpl',
        'body' => [
            [
                'type' => 'text',
            ]
        ]
    ]);
    expect($component->toArray())->toBe([
        'type' => 'tpl',
        'body' => [
            [
                'level' => 'primary',
                'type' => 'text',
            ],
        ]
    ]);
});

test('toArray with components config, change type', function () {
    // 可以用作设计自定义组件
    ConfigHelper::$testConfig = [
        'components.typeSelectMulti' => fn() => [
            'schema' => [
                'type' => 'select',
                'multiple' => true,
            ],
        ],
        'components.typeSelect' => [
            'schema' => [
                'clearable' => true,
            ],
        ],
    ];

    $component = Component::make([
        'type' => 'select-multi',
    ]);
    expect($component->toArray())->toBe([
        'clearable' => true,
        'type' => 'select',
        'multiple' => true,
    ]);
});

test('toArray with components config, static components', function () {
    ConfigHelper::$testConfig = [
        'components.typeImage' => fn() => [
            'schema' => [
                'enlargeAble' => true,
            ],
        ],
    ];

    $component = DetailAttribute::make()->typeImage();
    expect($component->get('type'))->toBe('static-image')
        ->and($component->toArray())->toBe([
            'enlargeAble' => true,
            'type' => 'static-image',
            'name' => '',
        ]);
});