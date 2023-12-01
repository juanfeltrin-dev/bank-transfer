<?php

declare(strict_types=1);

namespace App\Enum;

enum AccountType: int
{
    case NaturalPerson = 1;
    case LegalPerson = 2;
}
