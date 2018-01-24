<?php

namespace Solarium\Tests;

use PHPUnit\Framework\TestCase;
use Solarium\Client;

class ClientTest extends TestCase
{
    public function testVersion()
    {
        $version = Client::VERSION;
        $this->assertNotNull($version);
    }

    public function testCheckExact()
    {
        $this->assertTrue(
            Client::checkExact(Client::VERSION)
        );
    }

    public function testCheckExactPartial()
    {
        $this->assertTrue(
            Client::checkExact(substr(Client::VERSION, 0, 1))
        );
    }

    public function testCheckExactLower()
    {
        $this->assertFalse(
            Client::checkExact('0.1')
        );
    }

    public function testCheckExactHigher()
    {
        $this->assertFalse(
            Client::checkExact('99.0')
        );
    }

    public function testCheckMinimal()
    {
        $this->assertTrue(
            Client::checkMinimal(Client::VERSION)
        );
    }

    public function testCheckMinimalPartial()
    {
        $version = substr(Client::VERSION, 0, 1);

        $this->assertTrue(
            Client::checkMinimal($version)
        );
    }

    public function testCheckMinimalLower()
    {
        $this->assertTrue(
            Client::checkMinimal('0.1.0')
        );
    }

    public function testCheckMinimalHigher()
    {
        $this->assertFalse(
            Client::checkMinimal('99.0')
        );
    }
}
