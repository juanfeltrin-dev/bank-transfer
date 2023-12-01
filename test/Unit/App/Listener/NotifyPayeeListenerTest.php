<?php

declare(strict_types=1);

namespace Unit\App\Listener;

use App\Entity\Account;
use App\Enum\AccountType;
use App\Event\CreditAuthorizedPayeeEvent;
use App\Listener\NotifyPayeeListener;
use App\Service\NotificationServiceInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NotifyPayeeListenerTest extends TestCase
{
    public function testShouldNotifyPayee(): void
    {
        // arrange
        $notificationService = m::mock(NotificationServiceInterface::class);
        $logger = m::mock(LoggerInterface::class);
        $notifyPayeeListener = new NotifyPayeeListener($notificationService, $logger);

        $payeeAccount = new Account(
            'a4fa6905-3557-4980-a637-f82e3fea232f',
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            10000,
            AccountType::NaturalPerson
        );
        $event = new CreditAuthorizedPayeeEvent($payeeAccount);

        $notificationService->shouldReceive('notify')->with($payeeAccount->getEmail())->andReturn(true);
        $logger->shouldReceive('info')->with('Payee has been notified');

        // act
        $notifyPayeeListener->process($event);

        // assert
        $this->assertTrue(true);
    }
}
