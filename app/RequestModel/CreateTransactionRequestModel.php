<?php

namespace App\RequestModel;

class CreateTransactionRequestModel
{
    public function __construct(
        private ?string $payeeID,
        private ?string $payerID,
        private ?int $amount
    ) {
    }

    public function getPayeeID(): string
    {
        return $this->payeeID;
    }

    public function getPayerID(): string
    {
        return $this->payerID;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}