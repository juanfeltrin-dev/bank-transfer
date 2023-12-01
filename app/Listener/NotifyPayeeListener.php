<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\CreditAuthorizedPayeeEvent;
use App\Service\NotificationServiceInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;

class NotifyPayeeListener implements ListenerInterface
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
        private LoggerInterface $logger
    ) {
    }

    public function listen(): array
    {
        return [
            CreditAuthorizedPayeeEvent::class,
        ];
    }

    /**
     * @param CreditAuthorizedPayeeEvent $event
     */
    public function process(object $event): void
    {
        if ($this->notificationService->notify($event->payee->getEmail())) {
            $this->logger->info('Payee has been notified');

            return;
        }

        // TODO: Enviar para uma fila de fallback
    }
}
