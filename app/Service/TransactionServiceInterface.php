<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InsufficientFundsException;
use App\Exception\ResourceNotFoundException;
use App\Exception\RetailerPayerException;
use App\RequestModel\CreateTransactionRequestModel;

interface TransactionServiceInterface
{
    /**
     * @throws ResourceNotFoundException
     * @throws RetailerPayerException
     * @throws InsufficientFundsException
     */
    public function create(CreateTransactionRequestModel $createTransactionRequestModel): void;
}
