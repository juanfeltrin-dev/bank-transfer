<?php

declare(strict_types=1);

namespace Unit\App\Util;

use App\Util\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testShouldWhenSetDateThenReturnDateTimeInterface(): void
    {
        // arrange
        $date = new Date();

        // act
        $result = $date->get('2023-02-01');

        // assert
        $this->assertSame('01/02/2023', $result->format('d/m/Y'));
    }
}
