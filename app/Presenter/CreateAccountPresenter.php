<?php

declare(strict_types=1);

namespace App\Presenter;

use App\ResponseModel\CreateAccountResponseModel;

class CreateAccountPresenter
{
    public function present(CreateAccountResponseModel $createAccountResponseModel): array
    {
        return [
            'id' => $createAccountResponseModel->id,
            'name' => $createAccountResponseModel->name,
            'balance' => (float) substr_replace($createAccountResponseModel->balance, '.', -2, 0),
        ];
    }
}
