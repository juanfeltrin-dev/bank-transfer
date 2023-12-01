<?php

namespace Unit\App\Presenter;

use App\Presenter\CreateAccountPresenter;
use App\ResponseModel\CreateAccountResponseModel;
use Unit\TestCase;

class CreateAccountPresenterTest extends TestCase
{
    public function testShouldFormatAmountToMoneyPattern(): void
    {
        // arrange
        $createAccountPresenter = new CreateAccountPresenter();
        $createAccountResponseModel = new CreateAccountResponseModel(
            'a4fa6905-3557-4980-a637-f82e3fea232f',
            'Fulano',
            10000
        );

        // act
        $result = $createAccountPresenter->present($createAccountResponseModel);

        // assert
        $this->assertSame(100.00, $result['balance']);
    }
}