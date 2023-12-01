<?php

declare(strict_types=1);

namespace Unit\App\Service;

use App\Client\Http\NotificationClient;
use App\Entity\Account;
use App\Entity\User;
use App\Enum\AccountType;
use App\Enum\UserType;
use App\Service\NotificationService;
use Mockery as m;
use Unit\TestCase;


class NotificationServiceTest extends TestCase
{
    public function testNotificationServiceWhenRequestReturnTrueShouldReturnTrue(): void
    {
        // arrange
        $notificationClient = m::mock(NotificationClient::class);
        $service = new NotificationService($notificationClient);
        $notificationClient->shouldReceive('request')->andReturn(['message' => true]);

        // act
        $result = $service->notify('ciclano@gmail.com');

        // assert
        $this->assertTrue($result);
    }

    public function testNotificationServiceWhenRequestReturnFalseShouldReturnFalse(): void
    {
        // arrange
        $notificationClient = m::mock(NotificationClient::class);
        $service = new NotificationService($notificationClient);
        $notificationClient->shouldReceive('request')->andReturn(['message' => false]);

        // act
        $result = $service->notify('ciclano@gmail.com');

        // assert
        $this->assertFalse($result);
    }

    public function testNotificationServiceWhenRequestReturnNullShouldReturnFalse(): void
    {
        // arrange
        $notificationClient = m::mock(NotificationClient::class);
        $service = new NotificationService($notificationClient);
        $notificationClient->shouldReceive('request')->andReturn(null);
        $result = $service->notify('ciclano@gmail.com');

        // act
        $result = $service->notify('ciclano@gmail.com');

        // assert
        $this->assertFalse($result);
    }

    public function testNotificationServiceWhenRequestReturnEmptyShouldReturnFalse(): void
    {
        // arrange
        $notificationClient = m::mock(NotificationClient::class);
        $service = new NotificationService($notificationClient);
        $notificationClient->shouldReceive('request')->andReturn([]);
        $result = $service->notify('ciclano@gmail.com');

        // act
        $result = $service->notify('ciclano@gmail.com');

        // assert
        $this->assertFalse($result);
    }
}
