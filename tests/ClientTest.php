<?php

namespace Solarium\Tests;

use PHPUnit\Framework\TestCase;
use Solarium\Client;

class ClientTest extends TestCase
{
    public function testGetVersion()
    {
        $version = Client::getVersion();
        $this->assertNotNull($version);
    }

    public function testCheckExact()
    {
        $this->assertTrue(
            Client::checkExact(Client::getVersion())
        );
    }

    public function test76_5_4()
    {
        $this->assertTrue(
            // 76.5.4 is the version tag we use within github actions.
            Client::checkExact('76.5.4')
        );
    }

    public function testCheckExactPartial()
    {
        $this->assertTrue(
            Client::checkExact(substr(Client::getVersion(), 0, 1))
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
            Client::checkMinimal(Client::getVersion())
        );
    }

    public function testCheckMinimalPartial()
    {
        $version = substr(Client::getVersion(), 0, 1);

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
