<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Account;

class CreditAccountEvent
{
    public function __construct(
        public readonly Account $payee,
        public readonly int $amount
    ) {
    }
}
