<?php

declare(strict_types=1);
return [
    'default' => [
        'handler' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => 'php://stdout',
                'level' => \Monolog\Level::Info,
            ],
        ],
        'formatter' => [
            'class' => \Monolog\Formatter\JsonFormatter::class,
            'constructor' => [],
        ],
    ],
];
