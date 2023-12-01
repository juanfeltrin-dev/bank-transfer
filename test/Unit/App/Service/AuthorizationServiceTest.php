<?php

declare(strict_types=1);

namespace Unit\App\Service;

use App\Client\Http\AuthorizationClient;
use App\Enum\AuthorizationStatus;
use App\Service\AuthorizationService;
use Mockery as m;
use Unit\TestCase;


class AuthorizationServiceTest extends TestCase
{
    public function testAuthorizationServiceWhenRequestReturnAuthorizadShouldReturnAuthorizationWithAuthorized(): void
    {
        // arrange
        $authorizationClient = m::mock(AuthorizationClient::class);
        $service = new AuthorizationService($authorizationClient);
        $expected = AuthorizationStatus::Authorized;
        $authorizationClient->shouldReceive('request')->andReturn(['message' => 'Autorizado']);

        // act
        $result = $service->authorize();

        // assert
        $this->assertEquals($expected, $result);
    }

    public function testAuthorizationServiceWhenRequestReturnNotAuthorizadShouldReturnAuthorizationWithNotAuthorized(): void
    {
        // arrange
        $authorizationClient = m::mock(AuthorizationClient::class);
        $service = new AuthorizationService($authorizationClient);
        $expected = AuthorizationStatus::NotAuthorized;
        $authorizationClient->shouldReceive('request')->andReturn(['message' => 'Não Autorizado']);

        // act
        $result = $service->authorize();

        // assert
        $this->assertEquals($expected, $result);
    }

    public function testAuthorizationServiceWhenRequestReturnNullShouldReturnErrorAuthorization(): void
    {
        // arrange
        $authorizationClient = m::mock(AuthorizationClient::class);
        $service = new AuthorizationService($authorizationClient);
        $expected = AuthorizationStatus::Error;
        $authorizationClient->shouldReceive('request')->andReturn(null);

        // act
        $result = $service->authorize();

        // assert
        $this->assertEquals($expected, $result);
    }

    public function testAuthorizationServiceWhenRequestReturnEmptyShouldReturnErrorAuthorization(): void
    {
        // arrange
        $authorizationClient = m::mock(AuthorizationClient::class);
        $service = new AuthorizationService($authorizationClient);
        $expected = AuthorizationStatus::Error;
        $authorizationClient->shouldReceive('request')->andReturn([]);

        // act
        $result = $service->authorize();

        // assert
        $this->assertEquals($expected, $result);
    }
}
