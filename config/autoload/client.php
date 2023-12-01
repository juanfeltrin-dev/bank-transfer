<?php

declare(strict_types=1);
return [
    'http' => [
        'authorization' => [
            'scheme' => env('AUTHORIZATION_CLIENT_HTTP_SCHEME'),
            'host' => env('AUTHORIZATION_CLIENT_HTTP_HOST'),
            'path' => env('AUTHORIZATION_CLIENT_HTTP_PATH'),
            'timeout' => env('AUTHORIZATION_CLIENT_HTTP_TIMEOUT'),
        ],
        'notification' => [
            'scheme' => env('NOTIFICATION_CLIENT_HTTP_SCHEME'),
            'host' => env('NOTIFICATION_CLIENT_HTTP_HOST'),
            'path' => env('NOTIFICATION_CLIENT_HTTP_PATH'),
            'timeout' => env('NOTIFICATION_CLIENT_HTTP_TIMEOUT'),
        ],
    ],
];
