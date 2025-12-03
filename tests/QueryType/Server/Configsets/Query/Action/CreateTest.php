<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Create;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;

class CreateTest extends TestCase
{
    protected Create $action;

    public function setUp(): void
    {
        $this->action = new Create();
    }

    public function testGetType(): void
    {
        $this->assertSame(ConfigsetsQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetName(): void
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetBaseConfigSet(): void
    {
        $this->action->setBaseConfigSet('test');
        $this->assertSame('test', $this->action->getBaseConfigSet());
    }

    public function testSetProperty(): void
    {
        $this->action->setProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getProperty('foo'));
    }

    public function testGetResultClass(): void
    {
        $this->assertSame(ConfigsetsResult::class, $this->action->getResultClass());
    }
}
