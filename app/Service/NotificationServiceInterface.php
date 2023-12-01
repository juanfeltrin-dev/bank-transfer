<?php

declare(strict_types=1);

namespace App\Service;

interface NotificationServiceInterface
{
    public function notify(string $email): bool;
}
