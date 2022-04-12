<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Create;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;

class CreateTest extends TestCase
{
    /**
     * @var Create
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Create();
    }

    public function testGetType()
    {
        $this->assertSame(ConfigsetsQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetBaseConfigSet()
    {
        $this->action->setBaseConfigSet('test');
        $this->assertSame('test', $this->action->getBaseConfigSet());
    }

    public function testSetProperty()
    {
        $this->action->setProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getProperty('foo'));
    }

    public function testGetResultClass()
    {
        $this->assertSame(ConfigsetsResult::class, $this->action->getResultClass());
    }
}
