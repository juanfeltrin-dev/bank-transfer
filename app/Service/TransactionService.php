<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\DebitAccountEvent;
use App\EventDispatcher\EventDispatcher;
use App\Exception\InsufficientFundsException;
use App\Exception\ResourceNotFoundException;
use App\Exception\RetailerPayerException;
use App\RequestModel\CreateTransactionRequestModel;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private AccountServiceInterface $accountService,
        private EventDispatcher $eventDispatcher
    ) {
    }

    /**
     * @throws ResourceNotFoundException
     * @throws RetailerPayerException
     * @throws InsufficientFundsException
     */
    public function create(CreateTransactionRequestModel $createTransactionRequestModel): void
    {
        $payer = $this->accountService->get($createTransactionRequestModel->getPayerID());

        if ($payer->isLegalPerson()) {
            throw new RetailerPayerException('Lojista não pode efetuar transferências');
        }

        if (! $payer->haveBalance($createTransactionRequestModel->getAmount())) {
            throw new InsufficientFundsException('Saldo insuficiente');
        }

        $payee = $this->accountService->get($createTransactionRequestModel->getPayeeID());

        $this->eventDispatcher->dispatch(new DebitAccountEvent($payer, $payee, $createTransactionRequestModel->getAmount()));
    }
}
