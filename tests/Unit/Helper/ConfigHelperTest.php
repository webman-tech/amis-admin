<?php

use WebmanTech\AmisAdmin\Helper\ConfigHelper;

beforeEach(function () {
    ConfigHelper::$isForTest = true;
    ConfigHelper::reset();
});

test('get', function () {
    ConfigHelper::$testConfig = [
        'abc' => '123',
    ];

    expect(ConfigHelper::get('abc'))->toBe('123')
        ->and(ConfigHelper::get('def', '456'))->toBe('456');
});

test('get with solveClosure', function () {
    ConfigHelper::$testConfig = [
        'fn' => fn() => '123',
    ];

    expect(ConfigHelper::get('fn', null, true))->toBe('123');
});

test('get with solveClosure cache', function () {
    $count = 0;
    ConfigHelper::$testConfig = [
        'fn' => function () use (&$count) {
            $count++;
            return '123';
        }
    ];

    expect(ConfigHelper::get('fn', null, true))->toBe('123')
        ->and($count)->toBe(1)
        ->and(ConfigHelper::get('fn', null, true))->toBe('123')
        ->and($count)->toBe(1);
});
