<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\ClusterStatus;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\ClusterStatusResult;

class ClusterStatusTest extends TestCase
{
    /**
     * @var ClusterStatus
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new ClusterStatus();
    }

    public function testGetType()
    {
        $this->assertSame(CollectionsQuery::ACTION_CLUSTERSTATUS, $this->action->getType());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetCollection()
    {
        $this->action->setCollection('test');
        $this->assertSame('test', $this->action->getCollection());
    }

    public function testSetShard()
    {
        $this->action->setShard('testshard');
        $this->assertSame('testshard', $this->action->getShard());
    }

    public function testSetRoute()
    {
        $this->action->setRoute('testroute');
        $this->assertSame('testroute', $this->action->getRoute());
    }

    public function testGetResultClass()
    {
        $this->assertSame(ClusterStatusResult::class, $this->action->getResultClass());
    }
}
