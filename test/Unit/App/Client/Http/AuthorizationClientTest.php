<?php

declare(strict_types=1);

namespace Unit\App\Client\Http;

use App\Adapter\Config\ConfigInterface;
use App\Client\Http\AuthorizationClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Unit\TestCase;


class AuthorizationClientTest extends TestCase
{
    public function testShouldAddRequestOptionsThenReturnSuccessfully(): void
    {
        // arrange
        $config = m::mock(ConfigInterface::class);
        $client = m::mock(ClientInterface::class);
        $authorizationClient = new AuthorizationClient($config, $client);

        $expected = [
            'foo' => 'bar',
        ];

        $responseInterface = m::mock(ResponseInterface::class);
        $streamInterface = m::mock(StreamInterface::class);

        $config->shouldReceive('get')->with('client.http.authorization.host')->andReturn('http://localhost');
        $config->shouldReceive('get')->with('client.http.authorization.path')->andReturn('/v1');
        $config->shouldReceive('get')->with('client.http.authorization.timeout')->andReturn(5);

        $client->shouldReceive('request')
            ->with('GET', 'http://localhost/v1', [
                'timeout' => 5,
            ])
            ->andReturn($responseInterface);

        $responseInterface->shouldReceive('getBody')->andReturn($streamInterface);
        $streamInterface->shouldReceive('getContents')->andReturn('{"foo": "bar"}');

        // act
        $result = $authorizationClient->request();

        // assert
        $this->assertSame($expected, $result);
    }

    public function testShouldAddRequestOptionsWhenThrowClientExceptionThenReturnNull(): void
    {
        // arrange
        $config = m::mock(ConfigInterface::class);
        $client = m::mock(ClientInterface::class);
        $authorizationClient = new AuthorizationClient($config, $client);

        $clientException = m::mock(ClientException::class);

        $config->shouldReceive('get')->with('client.http.authorization.host')->andReturn('http://localhost');
        $config->shouldReceive('get')->with('client.http.authorization.path')->andReturn('/v1');
        $config->shouldReceive('get')->with('client.http.authorization.timeout')->andReturn(5);

        $client->shouldReceive('request')
            ->with('GET', 'http://localhost/v1', [
                'timeout' => 5,
            ])
            ->andThrow($clientException);

        // act
        $result = $authorizationClient->request();

        // assert
        $this->assertNull($result);
    }

    public function testShouldAddRequestOptionsWhenThrowServerExceptionThenReturnNull(): void
    {
        // arrange
        $config = m::mock(ConfigInterface::class);
        $client = m::mock(ClientInterface::class);
        $authorizationClient = new AuthorizationClient($config, $client);

        $serverException = m::mock(ServerException::class);

        $config->shouldReceive('get')->with('client.http.authorization.host')->andReturn('http://localhost');
        $config->shouldReceive('get')->with('client.http.authorization.path')->andReturn('/v1');
        $config->shouldReceive('get')->with('client.http.authorization.timeout')->andReturn(5);

        $client->shouldReceive('request')
            ->with('GET', 'http://localhost/v1', [
                'timeout' => 5,
            ])
            ->andThrow($serverException);

        // act
        $result = $authorizationClient->request();

        // assert
        $this->assertNull($result);
    }
}
