<?php

namespace Solarium\Tests\Core\Client\Adapter;

trait ConnectionTimeoutAwareTestTrait
{
    public function testDefaultConnectionTimeout(): void
    {
        $this->assertNull($this->adapter->getConnectionTimeout());
    }

    public function testSetAndGetConnectionTimeout(): void
    {
        $this->adapter->setConnectionTimeout(20);
        $this->assertSame(20, $this->adapter->getConnectionTimeout());
    }

    public function testSetConnectionTimeoutNull(): void
    {
        $this->adapter->setConnectionTimeout(null);
        $this->assertNull($this->adapter->getConnectionTimeout());
    }
}
