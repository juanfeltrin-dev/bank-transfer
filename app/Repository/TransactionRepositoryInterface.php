<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionType;

interface TransactionRepositoryInterface
{
    public function create(
        Account $account,
        Account $referenceAccount,
        int $amount,
        TransactionType $type
    ): Transaction;

    public function sumAmount(string $accountID): int;
}
