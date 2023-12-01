<?php

declare(strict_types=1);

namespace Unit\App\Factory;

use App\Adapter\Config\ConfigInterface;
use App\Factory\CryptFactory;
use App\Util\Crypt;
use Mockery as m;
use Psr\Container\ContainerInterface;
use Unit\TestCase;


class CryptFactoryTest extends TestCase
{
    public function testShouldCreateCryptInstance(): void
    {
        // arrange'
        $container = m::mock(ContainerInterface::class);
        $config = m::mock(ConfigInterface::class);
        $crypt = new CryptFactory();

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $config->shouldReceive('get')->with('crypt.cipher_algo')->andReturn('algo');
        $config->shouldReceive('get')->with('crypt.key')->andReturn('key');
        $config->shouldReceive('get')->with('crypt.iv')->andReturn('iv');

        // act
        $result = $crypt($container);

        // assert
        $this->assertInstanceOf(Crypt::class, $result);
        $this->assertTrue(method_exists($result, 'encrypt'));
        $this->assertTrue(method_exists($result, 'decrypt'));
    }
}
