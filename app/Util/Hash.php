<?php

declare(strict_types=1);

namespace App\Util;

class Hash
{
    public function hash(string $data): string
    {
        return password_hash($data, PASSWORD_DEFAULT);
    }

    public function verify(string $data, string $hash): bool
    {
        return password_verify($data, $hash);
    }
}
