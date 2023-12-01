<?php

declare(strict_types=1);

namespace App\Repository;

use App\Adapter\Database\DatabaseInterface;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionType;
use App\Util\Date;
use Ramsey\Uuid\Uuid;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(private DatabaseInterface $database, private Date $date)
    {
    }

    public function create(
        Account $account,
        Account $referenceAccount,
        int $amount,
        TransactionType $type
    ): Transaction {
        $transactionID = Uuid::uuid7()->toString();

        $this->database->insert('INSERT INTO transactions (id, account_id, reference_account_id, amount, type, created_at) VALUES(:id, :account_id, :reference_account_id, :amount, :type, :created_at)', [
            'id' => $transactionID,
            'account_id' => $account->getID(),
            'reference_account_id' => $referenceAccount->getID(),
            'amount' => $amount,
            'type' => $type->value,
            'created_at' => $this->date->get(),
        ]);

        return new Transaction(
            $transactionID,
            $account,
            $referenceAccount,
            $amount,
            $type
        );
    }

    public function sumAmount(string $accountID): int
    {
        $result = $this->database->selectOne(
            'SELECT sum(amount) as sumAmount FROM transactions WHERE account_id = :account_id',
            [
                'account_id' => $accountID,
            ]
        );

        if (! $result) {
            return 0;
        }

        return (int) $result->sumAmount;
    }
}
