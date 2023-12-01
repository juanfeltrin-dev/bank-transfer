<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Account;

class DebitAuthorizedPayerEvent
{
    public function __construct(
        public readonly Account $payer,
        public readonly Account $payee,
        public readonly int $amount
    ) {
    }
}
