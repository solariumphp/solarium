<?php

namespace Solarium\Tests\Core\Client\Adapter;

trait ProxyAwareTestTrait
{
    public function testDefaultProxy(): void
    {
        $this->assertNull($this->adapter->getProxy());
    }

    public function testSetAndGetProxy(): void
    {
        $this->adapter->setProxy('proxy.example.org:1234');
        $this->assertSame('proxy.example.org:1234', $this->adapter->getProxy());
    }

    public function testSetProxyNull(): void
    {
        $this->adapter->setProxy(null);
        $this->assertNull($this->adapter->getProxy());
    }
}
