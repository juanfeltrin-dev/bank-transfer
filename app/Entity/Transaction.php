<?php

declare(strict_types=1);

namespace App\Entity;
use App\Enum\TransactionType;

class Transaction
{
    public function __construct(
        private readonly string $id,
        private readonly Account $account,
        private readonly Account $referenceAccount,
        private readonly int $amount,
        private readonly TransactionType $type
    ) {
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getReferenceAccount(): Account
    {
        return $this->referenceAccount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }
}
