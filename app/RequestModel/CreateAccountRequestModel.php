<?php

declare(strict_types=1);

namespace App\RequestModel;

class CreateAccountRequestModel
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $document,
        private readonly string $password,
        private readonly int $type,
        private readonly int $balance
    ) {
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}
