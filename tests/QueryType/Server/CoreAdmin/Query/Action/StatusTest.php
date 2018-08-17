<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Status;

class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Status();
    }

    public function testSetPath()
    {
        $this->action->setIndexInfo(true);
        $this->assertTrue($this->action->getIndexInfo());
    }

    public function testGetType()
    {
        $this->assertSame('STATUS', $this->action->getType());
    }
}
