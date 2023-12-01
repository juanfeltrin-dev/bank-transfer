<?php

declare(strict_types=1);

namespace App\Factory;

use App\Adapter\Config\ConfigInterface;
use App\Util\Crypt;
use Psr\Container\ContainerInterface;

class CryptFactory
{
    public function __invoke(ContainerInterface $container, array $parameters = []): Crypt
    {
        /** @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);
        $cipherAlgo = $config->get('crypt.cipher_algo');
        $key = $config->get('crypt.key');
        $initializationVector = $config->get('crypt.iv');

        return new Crypt($cipherAlgo, $key, $initializationVector);
    }
}
