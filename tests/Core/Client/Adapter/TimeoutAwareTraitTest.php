<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Adapter\TimeoutAwareTrait;

class TimeoutAwareTraitTest extends TestCase
{
    /**
     * @var DummyTimeoutAwareAdapter
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new DummyTimeoutAwareAdapter();
    }

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

class DummyTimeoutAwareAdapter implements TimeoutAwareInterface
{
    use TimeoutAwareTrait;
}
