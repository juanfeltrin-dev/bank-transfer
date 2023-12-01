<?php

declare(strict_types=1);
return [
    'cipher_algo' => env('CRYPT_CIPHER_ALGO'),
    'key' => env('CRYPT_KEY'),
    'iv' => env('CRYPT_IV'),
];
