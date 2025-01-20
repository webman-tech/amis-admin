<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!file_exists(__DIR__ . '/helpers.php')) {
    copy(__DIR__ . '/../vendor/workerman/webman-framework/src/support/helpers.php', __DIR__ . '/helpers.php');
}
require_once __DIR__ . '/helpers.php';

if (!file_exists(__DIR__ . '/../config')) {
    copy_dir(__DIR__ . '/config', __DIR__ . '/../config');
}

require __DIR__ . '/../vendor/workerman/webman-framework/src/support/bootstrap.php';