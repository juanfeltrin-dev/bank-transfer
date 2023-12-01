<?php

declare(strict_types=1);

namespace App\Enum;

enum TransactionType: int
{
    case Credit = 1;
    case Debit = 2;
    case Refund = 3;
    case First = 4;
}
