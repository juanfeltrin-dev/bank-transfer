<?php

declare(strict_types=1);

namespace Unit\App\Util;

use App\Util\Hash;
use Unit\TestCase;


class HashTest extends TestCase
{
    public function testHashWithCorrectPassword(): void
    {
        // arrange
        $hash = new Hash();
        $password = '123456';

        // act
        $result = $hash->hash($password);

        // assert
        $this->assertTrue($hash->verify($password, $result));
    }

    public function testHashWithIncorrectPassword(): void
    {
        // arrange
        $hash = new Hash();
        $password = '123456';

        // act
        $result = $hash->hash($password);

        // assert
        $this->assertFalse($hash->verify('09876655', $result));
    }
}
