<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\Create;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\CreateResult;

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
        $this->assertSame(CollectionsQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetRouterName()
    {
        $this->action->setRouterName('testrouter');
        $this->assertSame('testrouter', $this->action->getRouterName());
    }

    public function testSetNumShards()
    {
        $this->action->setNumShards(5);
        $this->assertSame(5, $this->action->getNumShards());
    }

    public function testSetShards()
    {
        $this->action->setShards('shard-a,shard-b');
        $this->assertSame('shard-a,shard-b', $this->action->getShards());
    }

    public function testSetCollectionConfigName()
    {
        $this->action->setCollectionConfigName('testconfigname');
        $this->assertSame('testconfigname', $this->action->getCollectionConfigName());
    }

    public function testSetProperty()
    {
        $this->action->setProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getProperty('foo'));
    }

    public function testGetResultClass()
    {
        $this->assertSame(CreateResult::class, $this->action->getResultClass());
    }
}
