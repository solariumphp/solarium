<?php

namespace Solarium\Tests\Core\Client\Adapter;

use Solarium\Core\Client\Adapter\TimeoutAwareInterface;

trait TimeoutAwareTestTrait
{
    public function testDefaultTimeout(): void
    {
        $this->assertSame(TimeoutAwareInterface::DEFAULT_TIMEOUT, $this->adapter->getTimeout());
    }

    public function testSetAndGetTimeout(): void
    {
        $this->adapter->setTimeout(10);
        $this->assertSame(10, $this->adapter->getTimeout());
    }
}
