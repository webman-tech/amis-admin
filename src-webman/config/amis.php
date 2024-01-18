<?php

use WebmanTech\AmisAdmin\Contracts\PageReaderInterface;
use WebmanTech\AmisAdmin\Contracts\ResponseInterface;
use WebmanTech\AmisAdmin\PageReader\FilePageReader;
use WebmanTech\AmisAdmin\Webman\WebmanResponse;

return [
    'modules' => [
        'admin' => [
            /**
             * @see \WebmanTech\AmisAdmin\Config::$config
             */
            ResponseInterface::class => function () {
                return new WebmanResponse();
            },
            PageReaderInterface::class => function () {
                return new FilePageReader(
                    base_path('resource/amis/admin-pages'),
                    '/admin/page'
                );
            },
            'layout_app' => [
                'amis_json' => [
                    'api' => '/admin/pages',
                ],
            ]
        ],
    ],
];
