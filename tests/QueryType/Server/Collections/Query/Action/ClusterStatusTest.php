<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\ClusterStatus;

class ClusterStatusTest extends TestCase
{
    /**
     * @var ClusterStatus
     */
    protected $action;

    public function setUp()
    {
        $this->action = new ClusterStatus();
    }

    public function testCollection()
    {
        $this->action->setCollection('test');
        $this->assertSame('test', $this->action->getCollection());
    }

    public function testGetType()
    {
        $this->assertSame('CLUSTERSTATUS', $this->action->getType());
    }
}
