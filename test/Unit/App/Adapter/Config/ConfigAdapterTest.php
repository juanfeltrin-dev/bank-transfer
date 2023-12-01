<?php

declare(strict_types=1);

namespace Unit\App\Adapter\Config;

use App\Adapter\Config\ConfigAdapter;
use Hyperf\Contract\ConfigInterface as Config;
use Mockery as m;
use Unit\TestCase;

class ConfigAdapterTest extends TestCase
{
    public function testShouldGetConfigKey(): void
    {
        // arrange
        $config = m::mock(Config::class);
        $configAdapter = new ConfigAdapter($config);

        $config->shouldReceive('get')->with('key', null)->andReturn('value');

        // act
        $result = $configAdapter->get('key');

        // assert
        $this->assertSame('value', $result);
    }
}
