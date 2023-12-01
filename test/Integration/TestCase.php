<?php

namespace Integration;

use Hyperf\Testing\Http\Client as ClientTestingHttp;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected ClientTestingHttp $client;
    
    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = make(ClientTestingHttp::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        exec('php bin/hyperf.php migrate:fresh');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}