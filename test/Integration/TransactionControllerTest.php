<?php

declare(strict_types=1);

namespace Integration;

use App\Adapter\Database\DatabaseInterface;
use App\Enum\AccountType;
use App\Repository\AccountRepositoryInterface;
use App\RequestModel\CreateAccountRequestModel;
use App\Service\AccountServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Hyperf\Context\ApplicationContext;
use Hyperf\Testing\Http\Client as ClientTestingHttp;

class TransactionControllerTest extends TestCase
{
    public function testShouldReturnTransaction(): void
    {
        // arrange
        $mock = new MockHandler([
            new Response(200, [], '{"message": "Autorizado"}'),
            new Response(200, [], '{"message": true}'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handlerStack]);
        $container = ApplicationContext::getContainer();
        $container->set(ClientInterface::class, $guzzleClient);
        $this->client = make(ClientTestingHttp::class, ['container' => ApplicationContext::setContainer($container)]);
        $database = make(DatabaseInterface::class);
        /** @var AccountServiceInterface $accountService */
        $accountService = make(AccountServiceInterface::class);

        // Payer
        $createAccountPayerRequestModel = new CreateAccountRequestModel(
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            '123456',
            AccountType::NaturalPerson->value,
            10000
        );
        $createAccountPayerResponseModel = $accountService->create($createAccountPayerRequestModel);

        // Payee
        $createAccountPayeeRequestModel = new CreateAccountRequestModel(
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            '123456',
            AccountType::LegalPerson->value,
            100000
        );
        $createAccountPayeeResponseModel = $accountService->create($createAccountPayeeRequestModel);

        // act
        $response = $this->client->request('post', '/transactions', [
            'form_params' => [
                'payer' => $createAccountPayerResponseModel->id,
                'payee' => $createAccountPayeeResponseModel->id,
                'amount' => 1000,
            ],
        ]);

        // assert
        $resultAccountDatabase = $database->selectOne(
            'SELECT balance FROM accounts WHERE id = :id LIMIT 1',
            [
                'id' => $createAccountPayerResponseModel->id,
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(9000, $resultAccountDatabase->balance);
    }

    public function testShouldThrowRetailerPayerException(): void
    {
        // arrange
        // $client = make(ClientTestingHttp::class, ['container' => ApplicationContext::getContainer()]);
        /** @var AccountRepositoryInterface $accountRepository */
        $accountRepository = make(AccountRepositoryInterface::class);

        // Payer
        $createAccountPayerRequestModel = new CreateAccountRequestModel(
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            '123456',
            AccountType::LegalPerson->value,
            10000
        );
        $payer = $accountRepository->create($createAccountPayerRequestModel);

        // Payee
        $createAccountPayeeRequestModel = new CreateAccountRequestModel(
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            '123456',
            AccountType::LegalPerson->value,
            100000
        );
        $payee = $accountRepository->create($createAccountPayeeRequestModel);

        // act
        $response = $this->client->request('post', '/transactions', [
            'form_params' => [
                'payer' => $payer->getID(),
                'payee' => $payee->getID(),
                'amount' => 1000,
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);

        // assert
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['message' => 'Lojista não pode efetuar transferências'], $responseData);
    }

    public function testShouldThrowInsufficientFundsException(): void
    {
        // arrange
        // $client = make(ClientTestingHttp::class, ['container' => ApplicationContext::getContainer()]);
        /** @var AccountRepositoryInterface $accountRepository */
        $accountRepository = make(AccountRepositoryInterface::class);

        // Payer
        $createAccountPayerRequestModel = new CreateAccountRequestModel(
            'Ciclano',
            'ciclano@gmail.com',
            '00000000001',
            '123456',
            AccountType::NaturalPerson->value,
            10000
        );
        $payer = $accountRepository->create($createAccountPayerRequestModel);

        // Payee
        $createAccountPayeeRequestModel = new CreateAccountRequestModel(
            'Fulano',
            'fulano@gmail.com',
            '00000000002',
            '123456',
            AccountType::LegalPerson->value,
            100000
        );
        $payee = $accountRepository->create($createAccountPayeeRequestModel);

        // act
        $response = $this->client->request('post', '/transactions', [
            'form_params' => [
                'payer' => $payer->getID(),
                'payee' => $payee->getID(),
                'amount' => 1000000,
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);

        // assert
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['message' => 'Saldo insuficiente'], $responseData);
    }
}
