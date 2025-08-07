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

    public function testGetType(): void
    {
        $this->assertSame(CollectionsQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetName(): void
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetAsync(): void
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetRouterName(): void
    {
        $this->action->setRouterName('testrouter');
        $this->assertSame('testrouter', $this->action->getRouterName());
    }

    public function testSetNumShards(): void
    {
        $this->action->setNumShards(5);
        $this->assertSame(5, $this->action->getNumShards());
    }

    public function testSetShards(): void
    {
        $this->action->setShards('shard-a,shard-b');
        $this->assertSame('shard-a,shard-b', $this->action->getShards());
    }

    public function testSetCollectionConfigName(): void
    {
        $this->action->setCollectionConfigName('testconfigname');
        $this->assertSame('testconfigname', $this->action->getCollectionConfigName());
    }

    public function testSetProperty(): void
    {
        $this->action->setProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getProperty('foo'));
    }

    public function testGetResultClass(): void
    {
        $this->assertSame(CreateResult::class, $this->action->getResultClass());
    }
}
