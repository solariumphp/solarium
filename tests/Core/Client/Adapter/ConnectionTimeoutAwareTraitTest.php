<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareInterface;
use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareTrait;

class ConnectionTimeoutAwareTraitTest extends TestCase
{
    /**
     * @var DummyConnectionTimeoutAwareAdapter
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new DummyConnectionTimeoutAwareAdapter();
    }

    public function testDefaultConnectionTimeout(): void
    {
        $this->assertNull($this->adapter->getConnectionTimeout());
    }

    public function testSetAndGetConnectionTimeout(): void
    {
        $this->adapter->setConnectionTimeout(10);
        $this->assertSame(10, $this->adapter->getConnectionTimeout());
    }

    public function testSetConnectionTimeoutNull(): void
    {
        $this->adapter->setConnectionTimeout(null);
        $this->assertNull($this->adapter->getConnectionTimeout());
    }
}

class DummyConnectionTimeoutAwareAdapter implements ConnectionTimeoutAwareInterface
{
    use ConnectionTimeoutAwareTrait;
}
