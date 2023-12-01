<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\AuthorizationStatus;

interface AuthorizationServiceInterface
{
    public function authorize(): ?AuthorizationStatus;
}
