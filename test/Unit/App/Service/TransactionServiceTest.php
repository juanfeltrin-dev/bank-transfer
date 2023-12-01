<?php

declare(strict_types=1);

namespace Unit\App\Service;

use App\Entity\Account;
use App\Enum\AccountType;
use App\Event\DebitAccountEvent;
use App\EventDispatcher\EventDispatcher;
use App\Exception\InsufficientFundsException;
use App\Exception\RetailerPayerException;
use App\RequestModel\CreateTransactionRequestModel;
use App\Service\AccountServiceInterface;
use App\Service\TransactionService;
use Mockery as m;
use Unit\TestCase;


class TransactionServiceTest extends TestCase
{
    public function testTransactionServiceCreateWhenPayerIsRetailerThenThrowRetailerPayerException(): void
    {
        // arrange
        $accountService = m::mock(AccountServiceInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $transactionService = new TransactionService($accountService, $eventDispatcher);

        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $createTransactionRequestModel = new CreateTransactionRequestModel(
            $payeeID,
            $payerID,
            10000
        );

        $account = new Account(
            $payerID,
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            100000,
            AccountType::LegalPerson
        );

        $accountService->shouldReceive('get')->with($payerID)->andReturn($account);

        $this->expectException(RetailerPayerException::class);

        // act
        $transactionService->create($createTransactionRequestModel);
    }

    public function testTransactionServiceCreateWhenPayerBalanceLessThanAmountThenThrowInsufficientFundsException(): void
    {
        // arrange
        $accountService = m::mock(AccountServiceInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $transactionService = new TransactionService($accountService, $eventDispatcher);

        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $createTransactionRequestModel = new CreateTransactionRequestModel(
            $payeeID,
            $payerID,
            10000
        );

        $account = new Account(
            $payerID,
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            10,
            AccountType::NaturalPerson
        );

        $accountService->shouldReceive('get')->with($payerID)->andReturn($account);

        $this->expectException(InsufficientFundsException::class);

        // act
        $transactionService->create($createTransactionRequestModel);
    }

    public function testTransactionServiceCreateWhenAskToCreateTransactionThenDispatchDebitAccountEvent(): void
    {
        // arrange
        $accountService = m::mock(AccountServiceInterface::class);
        $eventDispatcher = m::mock(EventDispatcher::class);
        $transactionService = new TransactionService($accountService, $eventDispatcher);

        $payeeID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $payerID = 'accc6c7b-c8f8-476d-a493-b4c8552b6756';
        $createTransactionRequestModel = new CreateTransactionRequestModel(
            $payeeID,
            $payerID,
            10000
        );

        $accountPayer = new Account(
            $payerID,
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            10000,
            AccountType::NaturalPerson
        );

        $accountPayee = new Account(
            $payeeID,
            'Ciclano',
            'ciclano@gmail.com',
            '00000000000',
            10,
            AccountType::LegalPerson
        );

        $accountService->shouldReceive('get')->with($payerID)->andReturn($accountPayer);
        $accountService->shouldReceive('get')->with($payeeID)->andReturn($accountPayee);
        $eventDispatcher->shouldReceive('dispatch')->with(m::type(DebitAccountEvent::class));

        // act
        $transactionService->create($createTransactionRequestModel);

        // assert
        $this->assertTrue(true);
    }
}
