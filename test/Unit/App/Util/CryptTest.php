<?php

declare(strict_types=1);

namespace Unit\App\Util;

use App\Util\Crypt;
use Unit\TestCase;


class CryptTest extends TestCase
{
    public function testEncrypt(): void
    {
        // arrange
        $crypt = new Crypt('aes-256-ctr', 'qwertyuiopasdfgh', 'qwertyuiopasdfgh');
        $rawData = 'testando';
        $encryptedData = $crypt->encrypt($rawData);
        $decryptedData = $crypt->decrypt($encryptedData);

        // assert
        $this->assertEquals($rawData, $decryptedData);
    }
}
