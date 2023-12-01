<?php

declare(strict_types=1);

namespace App\Factory;

use Hyperf\Logger\LoggerFactory as LoggerFactoryHyperf;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container, array $parameters = []): LoggerInterface
    {
        /** @var LoggerFactoryHyperf $loggerFactory */
        $loggerFactory = $container->get(LoggerFactoryHyperf::class);

        return $loggerFactory->get('log', 'default');
    }
}
