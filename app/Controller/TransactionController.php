<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CreateTransactionRequest;
use App\RequestModel\CreateTransactionRequestModel;
use App\Service\TransactionServiceInterface;

class TransactionController extends AbstractController
{
    public function __construct(private TransactionServiceInterface $transactionService)
    {
    }

    public function store(CreateTransactionRequest $request): array
    {
        $request->validated();
        $requestModel = new CreateTransactionRequestModel(
            $this->request->input('payee'),
            $this->request->input('payer'),
            $this->request->input('amount')
        );
        $this->transactionService->create($requestModel);

        return [];
    }
}
