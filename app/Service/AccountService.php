<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Enum\TransactionType;
use App\Exception\ResourceNotFoundException;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use App\RequestModel\CreateAccountRequestModel;
use App\ResponseModel\CreateAccountResponseModel;

class AccountService implements AccountServiceInterface
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function get(string $id): Account
    {
        return $this->accountRepository->get($id);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function create(
        CreateAccountRequestModel $createAccountRequestModel
    ): CreateAccountResponseModel {
        $account = $this->accountRepository->create($createAccountRequestModel);
        $this->transactionRepository->create(
            $account,
            $account,
            $createAccountRequestModel->getBalance(),
            TransactionType::First
        );

        return new CreateAccountResponseModel(
            $account->getID(),
            $account->getName(),
            $account->getBalance()
        );
    }
}
