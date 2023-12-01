<?php

namespace Unit\App\Listener;
use App\Adapter\Database\DatabaseInterface;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\AccountType;
use App\Enum\AuthorizationStatus;
use App\Enum\TransactionType;
use App\Event\DebitAccountEvent;
use App\Event\DebitAuthorizedPayerEvent;
use App\EventDispatcher\EventDispatcher;
use App\Listener\DebitAccountListener;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use App\Service\AuthorizationServiceInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;

class DebitAccountListenerTest extends TestCase
{
    public function testShouldCreateTransactionAndUpdateBalance(): void
    {
        // arrange
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $logger = m::mock(LoggerInterface::class);
        $authorization = m::mock(AuthorizationServiceInterface::class);
        $database = m::mock(DatabaseInterface::class);
        $creditAccountListener = new DebitAccountListener($transactionRepository, $accountRepository, $eventDispatcher, $logger, $authorization, $database);

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

        $event = new DebitAccountEvent($payerAccount, $payeeAccount, $amount);

        $database->shouldReceive('beginTransaction');
        $transactionRepository->shouldReceive('create')->with($payerAccount, $payeeAccount, $amount * -1, TransactionType::Debit)->andReturn($transaction);
        $logger->shouldReceive('info')->with('Debit Transaction created', [
            'transactionID' => $transaction->getID(),
            'accountID' => $payerAccount->getID(),
        ]);
        $authorization->shouldReceive('authorize')->andReturn(AuthorizationStatus::Authorized);
        $transactionRepository->shouldReceive('sumAmount')->with($payerAccount->getID())->andReturn($sumAmountFromTransactions);
        $accountRepository->shouldReceive('updateBalance')->with($payerAccount->getID(), $sumAmountFromTransactions);
        $logger->shouldReceive('info')->with('The payer was debited', [
            'transactionID' => $transaction->getID(),
            'accountID' => $payerAccount->getID(),
        ]);
        $eventDispatcher->shouldReceive('dispatch')->with(m::type(DebitAuthorizedPayerEvent::class));
        $database->shouldReceive('commit');

        // act
        $creditAccountListener->process($event);

        // assert
        $this->assertSame($transaction->getAccount(), $payerAccount);
        $this->assertSame($transaction->getReferenceAccount(), $payeeAccount);
        $this->assertSame($transaction->getAmount(), $amount * 1);
    }
    
    public function testShouldApplyRollbackWhenAuthorizationDenied(): void
    {
        // arrange
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $logger = m::mock(LoggerInterface::class);
        $authorization = m::mock(AuthorizationServiceInterface::class);
        $database = m::mock(DatabaseInterface::class);
        $debitAccountListener = new DebitAccountListener($transactionRepository, $accountRepository, $eventDispatcher, $logger, $authorization, $database);

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
            $payeeAccount,
            $payerAccount,
            $amount,
            TransactionType::Credit
        );

        $event = new DebitAccountEvent($payerAccount, $payeeAccount, $amount);

        $database->shouldReceive('beginTransaction');
        $transactionRepository->shouldReceive('create')->with($payerAccount, $payeeAccount, $amount * -1, TransactionType::Debit)->andReturn($transaction);
        $logger->shouldReceive('info')->with('Debit Transaction created', [
            'transactionID' => $transaction->getID(),
            'accountID' => $payerAccount->getID(),
        ]);
        $authorization->shouldReceive('authorize')->andReturn(AuthorizationStatus::NotAuthorized);
        $database->shouldReceive('rollBack');
        $logger->shouldReceive('error')->with('An error occurred while debiting', [
            'exception' => 'There was a problem authorizing the transaction',
        ]);

        // act
        $debitAccountListener->process($event);

        // assert
        $this->assertTrue(true);
    }
}