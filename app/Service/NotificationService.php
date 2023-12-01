<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\Http\NotificationClient;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(private NotificationClient $client)
    {
    }

    public function notify(string $email): bool
    {
        $data = $this->client->request();

        if (! $data || ! isset($data['message'])) {
            return false;
        }

        return $data['message'];
    }
}
