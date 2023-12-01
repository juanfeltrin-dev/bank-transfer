<?php

declare(strict_types=1);

namespace App\Util;

class Crypt
{
    public function __construct(private string $cipherAlgo, private string $key, private string $initializationVector)
    {
    }

    public function encrypt($data): string
    {
        $ciphertext = openssl_encrypt(
            $data,
            $this->cipherAlgo,
            $this->key,
            OPENSSL_RAW_DATA,
            $this->initializationVector
        );

        return base64_encode($ciphertext);
    }

    public function decrypt($data): string
    {
        $decoded = base64_decode($data);

        return openssl_decrypt(
            $decoded,
            $this->cipherAlgo,
            $this->key,
            OPENSSL_RAW_DATA,
            $this->initializationVector
        );
    }
}
