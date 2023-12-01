<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\Http\AuthorizationClient;
use App\Enum\AuthorizationStatus;

class AuthorizationService implements AuthorizationServiceInterface
{
    public function __construct(private AuthorizationClient $client)
    {
    }

    public function authorize(): ?AuthorizationStatus
    {
        $data = $this->client->request();

        return $this->formatMessage($data);
    }

    private function formatMessage(?array $data): ?AuthorizationStatus
    {
        if (! $data || ! isset($data['message'])) {
            return AuthorizationStatus::Error;
        }

        return AuthorizationStatus::tryFrom($data['message']);
    }
}
