<?php

declare(strict_types=1);

namespace App\Listener;

use App\Adapter\Database\DatabaseInterface;
use App\Enum\AuthorizationStatus;
use App\Enum\TransactionType;
use App\Event\DebitAccountEvent;
use App\Event\DebitAuthorizedPayerEvent;
use App\EventDispatcher\EventDispatcher;
use App\Exception\AuthorizationFailedException;
use App\Repository\AccountRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use App\Service\AuthorizationServiceInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class DebitAccountListener implements ListenerInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcher $eventDispatcher,
        private LoggerInterface $logger,
        private AuthorizationServiceInterface $authorizationService,
        private DatabaseInterface $database
    ) {
    }

    public function listen(): array
    {
        return [
            DebitAccountEvent::class,
        ];
    }

    /**
     * @param DebitAccountEvent $event
     */
    public function process(object $event): void
    {
        $payer = $event->payer;
        $payee = $event->payee;
        $amount = $event->amount;

        $this->database->beginTransaction();
        try {
            $transaction = $this->transactionRepository->create($payer, $payee, $amount * -1, TransactionType::Debit);
            $authorizationStatus = $this->authorizationService->authorize();
            if ($authorizationStatus !== AuthorizationStatus::Authorized) {
                throw new AuthorizationFailedException('There was a problem authorizing the transaction');
            }

            $sumAmountFromTransactions = $this->transactionRepository->sumAmount($payer->getID());

            $this->accountRepository->updateBalance($payer->getID(), $sumAmountFromTransactions);
            $this->logger->info('The payer was debited', [
                'transactionID' => $transaction->getID(),
                'accountID' => $payer->getID(),
            ]);
            $this->eventDispatcher->dispatch(new DebitAuthorizedPayerEvent($payer, $payee, $amount));
            $this->database->commit();
        } catch (Throwable $exception) {
            $this->database->rollBack();
            $this->logger->error('An error occurred while debiting', [
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
