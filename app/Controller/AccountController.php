<?php

declare(strict_types=1);

namespace App\Controller;

use App\Presenter\CreateAccountPresenter;
use App\Request\CreateAccountRequest;
use App\RequestModel\CreateAccountRequestModel;
use App\Service\AccountServiceInterface;

class AccountController extends AbstractController
{
    public function __construct(
        private AccountServiceInterface $accountService,
        private CreateAccountPresenter $createAccountPresenter
    ) {
    }

    public function store(CreateAccountRequest $request): array
    {
        $request->validated();
        $requestModel = new CreateAccountRequestModel(
            $this->request->input('name'),
            $this->request->input('email'),
            $this->request->input('document'),
            $this->request->input('password'),
            $this->request->input('type'),
            $this->request->input('balance'),
        );

        $createAccountResponseModel = $this->accountService->create($requestModel);

        return $this->createAccountPresenter->present($createAccountResponseModel);
    }
}
