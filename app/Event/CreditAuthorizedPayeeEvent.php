<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Account;

class CreditAuthorizedPayeeEvent
{
    public function __construct(
        public readonly Account $payee
    ) {
    }
}
