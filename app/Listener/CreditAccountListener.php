<?php

declare(strict_types=1);

namespace App\Listener;

use App\Adapter\Database\DatabaseInterface;
use App\Enum\TransactionType;
use App\Event\CreditAuthorizedPayeeEvent;
use App\Event\DebitAuthorizedPayerEvent;
use App\Event\TransactionFailedEvent;
use App\EventDispatcher\EventDispatcher;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CreditAccountListener implements ListenerInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcher $eventDispatcher,
        private LoggerInterface $logger,
        private DatabaseInterface $database
    ) {
    }

    public function listen(): array
    {
        return [
            DebitAuthorizedPayerEvent::class,
        ];
    }

    /**
     * @param DebitAuthorizedPayerEvent $event
     */
    public function process(object $event): void
    {
        $payer = $event->payer;
        $payee = $event->payee;
        $amount = $event->amount;

        $this->database->beginTransaction();
        try {
            $transaction = $this->transactionRepository->create($payee, $payer, $amount, TransactionType::Credit);
            $this->logger->info('Credit Transaction created', [
                'transactionID' => $transaction->getID(),
                'accountID' => $payee->getID(),
            ]);
            $sumAmountFromTransactions = $this->transactionRepository->sumAmount($payee->getID());
            $this->accountRepository->updateBalance($payee->getID(), $sumAmountFromTransactions);
            $this->logger->info('The payee was credited', [
                'transactionID' => $transaction->getID(),
                'from' => $payer->getID(),
                'to' => $payee->getID(),
                'amount' => $amount,
            ]);
            $this->eventDispatcher->dispatch(new CreditAuthorizedPayeeEvent($payee));
            $this->database->commit();
        } catch (Throwable $exception) {
            $this->database->rollBack();
            $this->logger->error('An error occurred when crediting the payee', [
                'exception' => $exception->getMessage(),
            ]);
            $this->eventDispatcher->dispatch(new TransactionFailedEvent($payer, $payee, $amount));
        }
    }
}
