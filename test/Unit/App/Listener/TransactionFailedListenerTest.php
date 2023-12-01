<?php

declare(strict_types=1);

namespace Unit\App\Listener;

use App\Adapter\Database\DatabaseInterface;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\AccountType;
use App\Enum\TransactionType;
use App\Event\TransactionFailedEvent;
use App\EventDispatcher\EventDispatcher;
use App\Listener\TransactionFailedListener;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TransactionFailedListenerTest extends TestCase
{
    public function testShouldRefundPayerAndUpdateBalance(): void
    {
        // arrange
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $logger = m::mock(LoggerInterface::class);
        $database = m::mock(DatabaseInterface::class);
        $transactionFailedListener = new TransactionFailedListener($transactionRepository, $accountRepository, $eventDispatcher, $logger, $database);

        $sumAmountFromTransactions = 999900;
        $amount = 100;
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $payerAccount = new Account(
            $payerID,
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            10000000,
            AccountType::LegalPerson
        );
        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payeeAccount = new Account(
            $payeeID,
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            10000,
            AccountType::NaturalPerson
        );
        $transaction = new Transaction(
            'eeef26bb-4f94-496a-8d18-a78882fb9b16',
            $payerAccount,
            $payeeAccount,
            $amount,
            TransactionType::Credit
        );
        $event = new TransactionFailedEvent($payerAccount, $payeeAccount, $amount);

        $database->shouldReceive('beginTransaction');
        $transactionRepository->shouldReceive('create')->with($payerAccount, $payeeAccount, $amount, TransactionType::Refund)->andReturn($transaction);
        $transactionRepository->shouldReceive('sumAmount')->with($payerAccount->getID())->andReturn($sumAmountFromTransactions);
        $accountRepository->shouldReceive('updateBalance')->with($payerAccount->getID(), $sumAmountFromTransactions);
        $logger->shouldReceive('info')->with('The payee was credited', [
            'transactionID' => $transaction->getID(),
        ]);
        $database->shouldReceive('commit');

        // act
        $transactionFailedListener->process($event);

        // assert
        $this->assertTrue(true);
    }

    public function testShouldRollbackTransactionWhenThrowException(): void
    {
        // arrange
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $logger = m::mock(LoggerInterface::class);
        $database = m::mock(DatabaseInterface::class);
        $transactionFailedListener = new TransactionFailedListener($transactionRepository, $accountRepository, $eventDispatcher, $logger, $database);

        $amount = 100;
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $payerAccount = new Account(
            $payerID,
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            10000000,
            AccountType::LegalPerson
        );
        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payeeAccount = new Account(
            $payeeID,
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            10000,
            AccountType::NaturalPerson
        );

        $event = new TransactionFailedEvent($payerAccount, $payeeAccount, $amount);

        $database->shouldReceive('beginTransaction');
        $transactionRepository->shouldReceive('create')->with($payerAccount, $payeeAccount, $amount, TransactionType::Refund)->andThrow(new Exception('Error'));
        $database->shouldReceive('rollBack');
        $logger->shouldReceive('error')->with('An error occurred when crediting the payee', [
            'exception' => 'Error',
        ]);

        // act
        $transactionFailedListener->process($event);

        // assert
        $this->assertTrue(true);
    }
}
