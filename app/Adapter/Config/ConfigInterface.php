<?php

declare(strict_types=1);

namespace App\Adapter\Config;

interface ConfigInterface
{
    public function get(string $key, mixed $default = null): mixed;
}
