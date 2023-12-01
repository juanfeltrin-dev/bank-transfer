<?php

declare(strict_types=1);

namespace App\Repository;

use App\Adapter\Database\DatabaseInterface;
use App\Entity\Account;
use App\Enum\AccountType;
use App\Exception\ResourceNotFoundException;
use App\RequestModel\CreateAccountRequestModel;
use Ramsey\Uuid\Uuid;
use App\Util\Crypt;
use App\Util\Hash;
use App\Util\Date;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $database,
        private Crypt $crypt,
        private Hash $hash,
        private Date $date
    ) {
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function get(string $id): Account
    {
        $account = $this->database->selectOne('SELECT id, name, email, document, balance, type FROM accounts WHERE id = :id LIMIT 1', [
            'id' => $id,
        ]);

        if (! $account) {
            throw new ResourceNotFoundException('Account not found');
        }

        return new Account(
            $account->id,
            $account->name,
            $account->email,
            $account->document,
            $account->balance,
            AccountType::from($account->type),
        );
    }

    public function create(
        CreateAccountRequestModel $createAccountRequestModel
    ): Account {
        $accountID = Uuid::uuid7()->toString();
        $accountType = AccountType::from($createAccountRequestModel->getType());
        $document = $createAccountRequestModel->getDocument();
        $documentEncrypted = $this->crypt->encrypt($document);
        $dateNow = $this->date->get();

        $this->database->insert(
            'INSERT INTO accounts (id, name, email, document, password, balance, type, created_at, updated_at) ' . 
            'VALUES(:id, :name, :email, :document, :password, :balance, :type, :created_at, :updated_at)',
            [
                'id' => $accountID,
                'name' => $createAccountRequestModel->getName(),
                'email' => $createAccountRequestModel->getEmail(),
                'document' => $documentEncrypted,
                'password' => $this->hash->hash($createAccountRequestModel->getPassword()),
                'balance' => $createAccountRequestModel->getBalance(),
                'type' => $accountType->value,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ]
        );

        return new Account(
            $accountID,
            $createAccountRequestModel->getName(),
            $createAccountRequestModel->getEmail(),
            $document,
            $createAccountRequestModel->getBalance(),
            $accountType
        );
    }

    public function updateBalance(string $accountID, int $newBalance): void
    {
        $this->database->update('UPDATE accounts SET balance = :balance, updated_at = :updated_at WHERE id = :id', [
            'balance' => $newBalance,
            'id' => $accountID,
            'updated_at' => $this->date->get(),
        ]);
    }
}
