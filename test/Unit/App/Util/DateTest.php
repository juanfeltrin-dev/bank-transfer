<?php

namespace Unit\App\Util;
use PHPUnit\Framework\TestCase;
use App\Util\Date;

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