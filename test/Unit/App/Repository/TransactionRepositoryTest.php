<?php

declare(strict_types=1);

namespace Unit\App\Repository;

use App\Adapter\Database\DatabaseInterface;
use App\Entity\Account;
use App\Enum\AccountType;
use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use App\Util\Date;
use DateTime;
use Mockery as m;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Unit\TestCase;


class TransactionRepositoryTest extends TestCase
{
    public function testShouldCreateTransaction(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $date = m::mock(Date::class);
        $transactionRepository = new TransactionRepository($database, $date);

        $transactionID = '253e0f90-8842-4731-91dd-0191816e6a28';
        $uuid = Uuid::fromString($transactionID);
        $factoryMock = m::mock(UuidFactory::class . '[uuid7]', [
            'uuid7' => $uuid,
        ]);
        Uuid::setFactory($factoryMock);

        $dateTime = new DateTime('2023-01-01');
        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $amount = 1000;
        $account = new Account(
            $payerID,
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            1000000,
            AccountType::NaturalPerson
        );

        $referenceAccount = new Account(
            $payeeID,
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            1000000,
            AccountType::LegalPerson
        );

        $date->shouldReceive('get')->andReturn($dateTime);

        $database->shouldReceive('insert')
            ->with(
                'INSERT INTO transactions (id, account_id, reference_account_id, amount, type, created_at) VALUES(:id, :account_id, :reference_account_id, :amount, :type, :created_at)',
                [
                    'id' => $transactionID,
                    'account_id' => $account->getID(),
                    'reference_account_id' => $referenceAccount->getID(),
                    'amount' => $amount,
                    'type' => 1,
                    'created_at' => $dateTime,
                ]
            );

        // act
        $result = $transactionRepository->create($account, $referenceAccount, $amount, TransactionType::Credit);

        // assert
        $this->assertSame($transactionID, $result->getID());
        $this->assertSame(TransactionType::Credit, $result->getType());
    }
    
    public function testShouldGetSumAmount(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $date = m::mock(Date::class);
        $transactionRepository = new TransactionRepository($database, $date);

        $accountID = 'a4fa6905-3557-4980-a637-f82e3fea232f';


        $database->shouldReceive('selectOne')
            ->with(
                'SELECT sum(amount) as sumAmount FROM transactions WHERE account_id = :account_id', [
                    'account_id' => $accountID,
                ]
            )->andReturn((object) [
                'sumAmount' => 1000,
            ]);

        // act
        $result = $transactionRepository->sumAmount($accountID);

        // assert
        $this->assertSame(1000, $result);
    }
    
    public function testShouldGetSumAmountWhenAmountIsNullThenReturnZero(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $date = m::mock(Date::class);
        $transactionRepository = new TransactionRepository($database, $date);

        $accountID = 'a4fa6905-3557-4980-a637-f82e3fea232f';


        $database->shouldReceive('selectOne')
            ->with(
                'SELECT sum(amount) as sumAmount FROM transactions WHERE account_id = :account_id', [
                    'account_id' => $accountID,
                ]
            )->andReturn(null);

        // act
        $result = $transactionRepository->sumAmount($accountID);

        // assert
        $this->assertSame(0, $result);
    }
}
