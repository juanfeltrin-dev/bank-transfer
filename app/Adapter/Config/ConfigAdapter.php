<?php

declare(strict_types=1);

namespace App\Adapter\Config;

use Hyperf\Contract\ConfigInterface as Config;

class ConfigAdapter implements ConfigInterface
{
    public function __construct(private Config $config)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }
}
