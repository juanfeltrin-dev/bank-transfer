<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\InsufficientFundsException;
use App\Exception\ResourceNotFoundException;
use App\Exception\RetailerPayerException;
use App\Exception\TransactionDeniedException;
use App\Exception\Validation\ValidationException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));

        if (
            $throwable instanceof RetailerPayerException
            || $throwable instanceof InsufficientFundsException
            || $throwable instanceof TransactionDeniedException
        ) {
            return $response->withStatus(400)->withBody(new SwooleStream(json_encode(['message' => $throwable->getMessage()])));
        }

        if ($throwable instanceof ResourceNotFoundException) {
            return $response->withStatus(404)->withBody(new SwooleStream(json_encode(['message' => $throwable->getMessage()])));
        }

        if ($throwable instanceof ValidationException) {
            return $response->withStatus(422)->withBody(new SwooleStream(json_encode(['message' => $throwable->getMessage()])));
        }

        return $response->withStatus(500)->withBody(new SwooleStream(json_encode(['message' => 'Internal Server Error.'])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
