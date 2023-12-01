<?php

declare(strict_types=1);

namespace App\Listener;

use App\Adapter\Database\DatabaseInterface;
use App\Enum\TransactionType;
use App\Event\TransactionFailedEvent;
use App\EventDispatcher\EventDispatcher;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class TransactionFailedListener implements ListenerInterface
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
            TransactionFailedEvent::class,
        ];
    }

    /**
     * @param TransactionFailedEvent $event
     */
    public function process(object $event): void
    {
        $payer = $event->payer;
        $payee = $event->payee;
        $amount = $event->amount;

        $this->database->beginTransaction();
        try {
            $transaction = $this->transactionRepository->create($payer, $payee, $amount, TransactionType::Refund);
            $sumAmountFromTransactions = $this->transactionRepository->sumAmount($payer->getID());
            $this->accountRepository->updateBalance($payer->getID(), $sumAmountFromTransactions);
    
            $this->logger->info('The payee was credited', ['transactionID' => $transaction->getID()]);
            $this->database->commit();
        } catch (Throwable $exception) {
            $this->database->rollBack();
            $this->logger->error('An error occurred when crediting the payee', [
                'exception' => $exception->getMessage(),
            ]);

            // TODO: Enviar para um fila de fallback
        }
    }
}
