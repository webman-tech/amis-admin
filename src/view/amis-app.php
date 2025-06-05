<?php

$routeMode ??= 'hash';

$script = implode("\n", [
    "const routeMode = '{$routeMode}';",
    file_get_contents(__DIR__ . '/amis-app-history.js')
]);

require __DIR__ . '/_amis-basic.php';
