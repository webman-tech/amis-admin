<?php

use WebmanTech\AmisAdmin\Contracts\PageReaderInterface;
use WebmanTech\AmisAdmin\Contracts\RequestInterface;
use WebmanTech\AmisAdmin\Contracts\ResponseInterface;
use WebmanTech\AmisAdmin\Contracts\ValidatorInterface;
use WebmanTech\AmisAdmin\Impl\FilePageReader;
use WebmanTech\AmisAdmin\Impl\NullValidator;
use WebmanTech\AmisAdmin\Webman\WebmanResponse;

return [
    'modules' => [
        'admin' => [
            /**
             * @see \WebmanTech\AmisAdmin\Config::$config
             */
            ValidatorInterface::class => function () {
                return new NullValidator();
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
