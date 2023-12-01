<?php

declare(strict_types=1);
return [
    \App\Repository\AccountRepositoryInterface::class => \App\Repository\AccountRepository::class,
    \App\Repository\TransactionRepositoryInterface::class => \App\Repository\TransactionRepository::class,
    \GuzzleHttp\ClientInterface::class => \GuzzleHttp\Client::class,
    \App\Adapter\Config\ConfigInterface::class => \App\Adapter\Config\ConfigAdapter::class,
    \Psr\Log\LoggerInterface::class => \App\Factory\LoggerFactory::class,
    \App\Util\Crypt::class => \App\Factory\CryptFactory::class,
    \App\Adapter\Database\DatabaseInterface::class => \App\Adapter\Database\DatabaseAdapter::class,
    \App\Service\AccountServiceInterface::class => \App\Service\AccountService::class,
    \App\Service\TransactionServiceInterface::class => \App\Service\TransactionService::class,
    \App\Service\AuthorizationServiceInterface::class => \App\Service\AuthorizationService::class,
    \App\Service\NotificationServiceInterface::class => \App\Service\NotificationService::class,
];
