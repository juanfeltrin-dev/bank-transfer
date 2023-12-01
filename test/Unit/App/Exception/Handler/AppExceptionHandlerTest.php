<?php

declare(strict_types=1);

namespace Unit\App\Exception\Handler;

use App\Exception\Handler\AppExceptionHandler;
use App\Exception\InsufficientFundsException;
use App\Exception\ResourceNotFoundException;
use App\Exception\RetailerPayerException;
use App\Exception\TransactionDeniedException;
use Exception;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Unit\TestCase;


class AppExceptionHandlerTest extends TestCase
{
    public function testShouldWhenRetailerPayerExceptionThenReturn400StatusCode(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $response = m::mock(ResponseInterface::class);
        $exception = new RetailerPayerException('lojista não pode transferir dinheiro');
        $appExceptionHandler = new AppExceptionHandler($logger);

        $logger->shouldReceive('error')->with(sprintf('%s[%s] in %s', $exception->getMessage(), $exception->getLine(), $exception->getFile()));
        $response->shouldReceive('withStatus')->with(400)->andReturnSelf();
        $response->shouldReceive('withBody')->with(m::type(SwooleStream::class))->andReturnSelf();

        // act
        $result = $appExceptionHandler->handle($exception, $response);

        // assert
        $this->assertSame($response, $result);
    }

    public function testShouldWhenInsufficientFundsExceptionThenReturn400StatusCode(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $response = m::mock(ResponseInterface::class);
        $exception = new InsufficientFundsException('saldo indisponivel');
        $appExceptionHandler = new AppExceptionHandler($logger);

        $logger->shouldReceive('error')->with(sprintf('%s[%s] in %s', $exception->getMessage(), $exception->getLine(), $exception->getFile()));
        $response->shouldReceive('withStatus')->with(400)->andReturnSelf();
        $response->shouldReceive('withBody')->with(m::type(SwooleStream::class))->andReturnSelf();

        // act
        $result = $appExceptionHandler->handle($exception, $response);

        // assert
        $this->assertSame($response, $result);
    }

    public function testShouldWhenTransactionDeniedExceptionThenReturn400StatusCode(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $response = m::mock(ResponseInterface::class);
        $exception = new TransactionDeniedException('transação negada');
        $appExceptionHandler = new AppExceptionHandler($logger);

        $logger->shouldReceive('error')->with(sprintf('%s[%s] in %s', $exception->getMessage(), $exception->getLine(), $exception->getFile()));
        $response->shouldReceive('withStatus')->with(400)->andReturnSelf();
        $response->shouldReceive('withBody')->with(m::type(SwooleStream::class))->andReturnSelf();

        // act
        $result = $appExceptionHandler->handle($exception, $response);

        // assert
        $this->assertSame($response, $result);
    }

    public function testShouldWhenResourceNotFoundExceptionThenReturn404StatusCode(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $response = m::mock(ResponseInterface::class);
        $exception = new ResourceNotFoundException('not found');
        $appExceptionHandler = new AppExceptionHandler($logger);

        $logger->shouldReceive('error')->with(sprintf('%s[%s] in %s', $exception->getMessage(), $exception->getLine(), $exception->getFile()));
        $response->shouldReceive('withStatus')->with(404)->andReturnSelf();
        $response->shouldReceive('withBody')->with(m::type(SwooleStream::class))->andReturnSelf();

        // act
        $result = $appExceptionHandler->handle($exception, $response);

        // assert
        $this->assertSame($response, $result);
    }

    public function testShouldWhenExceptionThenReturn404StatusCode(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $response = m::mock(ResponseInterface::class);
        $exception = new Exception('error');
        $appExceptionHandler = new AppExceptionHandler($logger);

        $logger->shouldReceive('error')->with(sprintf('%s[%s] in %s', $exception->getMessage(), $exception->getLine(), $exception->getFile()));
        $response->shouldReceive('withStatus')->with(500)->andReturnSelf();
        $response->shouldReceive('withBody')->with(m::type(SwooleStream::class))->andReturnSelf();

        // act
        $result = $appExceptionHandler->handle($exception, $response);

        // assert
        $this->assertSame($response, $result);
    }

    public function testIsValidReturnTrue(): void
    {
        // arrange
        $logger = m::mock(LoggerInterface::class);
        $exception = new Exception('error');
        $appExceptionHandler = new AppExceptionHandler($logger);

        // act
        $result = $appExceptionHandler->isValid($exception);

        // assert
        $this->assertTrue($result);
    }
}
