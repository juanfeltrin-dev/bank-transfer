<?php

declare(strict_types=1);

namespace Integration;

use App\Adapter\Database\DatabaseInterface;

class AccountControllerTest extends TestCase
{
    public function testShouldCreateAccountAndCreateInitialTransaction(): void
    {
        // arrange
        $database = make(DatabaseInterface::class);
        
        // act
        $response = $this->client->request('post', '/accounts', [
            'form_params' => [
                'name' => 'Fulano',
                'email' => 'fulano@gmail.com',
                'document' => '00000000000',
                'password' => 'minhasenha',
                'type' => 1,
                'balance' => 100000,
            ],
        ]);

        // assert
        $resultAccountDatabase = $database->selectOne(
            'SELECT name, email, type, balance FROM accounts LIMIT 1'
        );
        $resultTransactionDatabase = $database->selectOne(
            'SELECT amount FROM transactions LIMIT 1'
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Fulano', $resultAccountDatabase->name);
        $this->assertEquals('fulano@gmail.com', $resultAccountDatabase->email);
        $this->assertEquals(1, $resultAccountDatabase->type);
        $this->assertEquals(100000, $resultAccountDatabase->balance);
        $this->assertEquals(100000, $resultTransactionDatabase->amount);
    }
}