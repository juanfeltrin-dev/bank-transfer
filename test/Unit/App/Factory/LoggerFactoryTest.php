<?php

declare(strict_types=1);

namespace Unit\App\Factory;

use App\Factory\LoggerFactory;
use Hyperf\Logger\LoggerFactory as LoggerFactoryHyperf;
use Mockery as m;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Unit\TestCase;


class LoggerFactoryTest extends TestCase
{
    public function testShouldCreateCryptInstance(): void
    {
        // arrange'
        $container = m::mock(ContainerInterface::class);
        $loggerFactoryFramework = m::mock(LoggerFactoryHyperf::class);
        $logger = m::mock(LoggerInterface::class);
        $loggerFactory = new LoggerFactory();

        $container->shouldReceive('get')->with(LoggerFactoryHyperf::class)->andReturn($loggerFactoryFramework);
        $loggerFactoryFramework->shouldReceive('get')->with('log', 'default')->andReturn($logger);

        // act
        $result = $loggerFactory($container);

        // assert
        $this->assertInstanceOf(LoggerInterface::class, $result);
        $this->assertTrue(method_exists($result, 'emergency'));
        $this->assertTrue(method_exists($result, 'alert'));
        $this->assertTrue(method_exists($result, 'critical'));
        $this->assertTrue(method_exists($result, 'error'));
        $this->assertTrue(method_exists($result, 'warning'));
        $this->assertTrue(method_exists($result, 'notice'));
        $this->assertTrue(method_exists($result, 'info'));
        $this->assertTrue(method_exists($result, 'debug'));
        $this->assertTrue(method_exists($result, 'log'));
    }
}
