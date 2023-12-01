<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Exception\ResourceNotFoundException;
use App\RequestModel\CreateAccountRequestModel;

interface AccountRepositoryInterface
{
    /**
     * @throws ResourceNotFoundException
     */
    public function get(string $id): Account;

    public function create(
        CreateAccountRequestModel $createAccountRequestModel
    ): Account;

    public function updateBalance(string $accountID, int $newBalance): void;
}
