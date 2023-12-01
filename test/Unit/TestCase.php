<?php

declare(strict_types=1);

namespace Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase as TestCasePHPUnit;


class TestCase extends TestCasePHPUnit
{
    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }
}
