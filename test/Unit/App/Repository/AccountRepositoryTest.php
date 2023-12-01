<?php

declare(strict_types=1);

namespace Unit\App\Repository;

use App\Adapter\Database\DatabaseInterface;
use App\Enum\AccountType;
use App\Exception\ResourceNotFoundException;
use App\Repository\AccountRepository;
use App\RequestModel\CreateAccountRequestModel;
use App\Util\Crypt;
use App\Util\Hash;
use App\Util\Date;
use DateTime;
use Mockery as m;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Unit\TestCase;


class AccountRepositoryTest extends TestCase
{
    public function testShouldCreateAccount(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $crypt = m::mock(Crypt::class);
        $hash = m::mock(Hash::class);
        $date = m::mock(Date::class);
        $accountRepository = new AccountRepository($database, $crypt, $hash, $date);

        $dateTime = new DateTime('2023-01-01');
        $accountID = '253e0f90-8842-4731-91dd-0191816e6a28';
        $uuid = Uuid::fromString($accountID);
        $factoryMock = m::mock(UuidFactory::class . '[uuid7]', [
            'uuid7' => $uuid,
        ]);
        Uuid::setFactory($factoryMock);
        $createAccountRequestModel = new CreateAccountRequestModel(
            'Fulano',
            'fulano@gmail.com',
            '00000000000',
            '123456',
            1,
            10000
        );

        $crypt->shouldReceive('encrypt')->with($createAccountRequestModel->getDocument())->andReturn('documentencrypted');
        $hash->shouldReceive('hash')->with($createAccountRequestModel->getPassword())->andReturn('passwordhashed');
        $date->shouldReceive('get')->andReturn($dateTime);
        $database->shouldReceive('insert')->with(
            'INSERT INTO accounts (id, name, email, document, password, balance, type, created_at, updated_at) ' . 
            'VALUES(:id, :name, :email, :document, :password, :balance, :type, :created_at, :updated_at)',
            [
                'id' => $accountID,
                'name' => $createAccountRequestModel->getName(),
                'email' => $createAccountRequestModel->getEmail(),
                'document' => 'documentencrypted',
                'password' => 'passwordhashed',
                'balance' => $createAccountRequestModel->getBalance(),
                'type' => $createAccountRequestModel->getType(),
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ]
        );

        // act
        $result = $accountRepository->create($createAccountRequestModel);

        // assert
        $this->assertSame(AccountType::NaturalPerson, $result->getType());
        $this->assertSame('00000000000', $result->getDocument());
    }

    public function testShouldGetAccount(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $crypt = m::mock(Crypt::class);
        $hash = m::mock(Hash::class);
        $date = m::mock(Date::class);
        $accountRepository = new AccountRepository($database, $crypt, $hash, $date);

        $id = '253e0f90-8842-4731-91dd-0191816e6a28';

        $database->shouldReceive('selectOne')->with('SELECT id, name, email, document, balance, type FROM accounts WHERE id = :id LIMIT 1', [
            'id' => $id,
        ])->andReturn((object) [
            'id' => $id,
            'name' => 'Fulano',
            'email' => 'fulano@gmail.com',
            'document' => '000000000',
            'balance' => 1000,
            'type' => 1
        ]);

        // act
        $result = $accountRepository->get($id);

        // assert
        $this->assertSame(AccountType::NaturalPerson, $result->getType());
        $this->assertSame('000000000', $result->getDocument());
    }

    public function testShouldWhenAccountNotFoundThenThrowException(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $crypt = m::mock(Crypt::class);
        $hash = m::mock(Hash::class);
        $date = m::mock(Date::class);
        $accountRepository = new AccountRepository($database, $crypt, $hash, $date);

        $id = '253e0f90-8842-4731-91dd-0191816e6a28';

        $database->shouldReceive('selectOne')->with('SELECT id, name, email, document, balance, type FROM accounts WHERE id = :id LIMIT 1', [
            'id' => $id,
        ])->andReturnNull();

        $this->expectException(ResourceNotFoundException::class);

        // act
        $accountRepository->get($id);
    }

    public function testShouldUpdateBalance(): void
    {
        // arrange
        $database = m::mock(DatabaseInterface::class);
        $crypt = m::mock(Crypt::class);
        $hash = m::mock(Hash::class);
        $date = m::mock(Date::class);
        $accountRepository = new AccountRepository($database, $crypt, $hash, $date);

        $dateTime = new DateTime('2023-01-01');
        $accountID = '253e0f90-8842-4731-91dd-0191816e6a28';
        $newBalance = 10000;

        $date->shouldReceive('get')->andReturn($dateTime);
        $database->shouldReceive('update')->with('UPDATE accounts SET balance = :balance, updated_at = :updated_at WHERE id = :id', [
            'balance' => $newBalance,
            'id' => $accountID,
            'updated_at' => $dateTime,
        ]);

        // act
        $accountRepository->updateBalance($accountID, $newBalance);

        // assert
        $this->assertTrue(true);
    }
}
