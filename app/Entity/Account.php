<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AccountType;

class Account
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $email,
        private readonly string $document,
        private readonly int $balance,
        private readonly AccountType $type
    ) {
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getType(): AccountType
    {
        return $this->type;
    }

    public function isLegalPerson(): bool
    {
        return $this->type === AccountType::LegalPerson;
    }

    public function haveBalance(int $amount): bool
    {
        $balance = $this->balance - $amount;

        return $balance >= 0;
    }
}
