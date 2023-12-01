<?php

namespace Unit\App\Service;
use App\Entity\Account;
use App\Enum\AccountType;
use App\Enum\TransactionType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use App\RequestModel\CreateAccountRequestModel;
use App\Service\AccountService;
use Unit\TestCase;
use Mockery as m;

class AccountServiceTest extends TestCase
{
    public function testShouldGetAccount(): void
    {
        // arrange
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountService = new AccountService($accountRepository, $transactionRepository);

        $accountID = 'a4fa6905-3557-4980-a637-f82e3fea232f';
        $account = new Account(
            'a4fa6905-3557-4980-a637-f82e3fea232f',
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            1000,
            AccountType::NaturalPerson,
        );

        $accountRepository->shouldReceive('get')->with($accountID)->andReturn($account);

        // action
        $result = $accountService->get($accountID);

        // assert
        $this->assertSame($account, $result);
    }
    
    public function testShouldCreateAccount(): void
    {
        // arrange
        $accountRepository = m::mock(AccountRepositoryInterface::class);
        $transactionRepository = m::mock(TransactionRepositoryInterface::class);
        $accountService = new AccountService($accountRepository, $transactionRepository);

        $createAccountRequestModel = new CreateAccountRequestModel(
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            'senha',
            1,
            10000,
        );
        $account = new Account(
            'a4fa6905-3557-4980-a637-f82e3fea232f',
            $createAccountRequestModel->getName(),
            $createAccountRequestModel->getEmail(),
            $createAccountRequestModel->getDocument(),
            $createAccountRequestModel->getBalance(),
            AccountType::NaturalPerson,
        );

        $accountRepository->shouldReceive('create')->with($createAccountRequestModel)->andReturn($account);
        $transactionRepository->shouldReceive('create')->with($account, $account, $createAccountRequestModel->getBalance(), TransactionType::First);

        // action
        $result = $accountService->create($createAccountRequestModel);

        // assert
        $this->assertSame($account->getID(), $result->id);
        $this->assertSame($account->getName(), $result->name);
        $this->assertSame($account->getBalance(), $createAccountRequestModel->getBalance());
        $this->assertSame($account->getName(), $createAccountRequestModel->getName());
    }
}