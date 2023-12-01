<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Exception\ResourceNotFoundException;
use App\RequestModel\CreateAccountRequestModel;
use App\ResponseModel\CreateAccountResponseModel;

interface AccountServiceInterface
{
    /**
     * @throws ResourceNotFoundException
     */
    public function get(string $id): Account;

    /**
     * @throws ResourceNotFoundException
     */
    public function create(
        CreateAccountRequestModel $createAccountRequestModel
    ): CreateAccountResponseModel;
}