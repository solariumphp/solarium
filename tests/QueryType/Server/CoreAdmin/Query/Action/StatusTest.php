<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Status;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Status();
    }

    public function testGetType()
    {
        $this->assertSame(CoreAdminQuery::ACTION_STATUS, $this->action->getType());
    }

    public function testSetCore()
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }

    public function testSetIndexInfo()
    {
        $this->action->setIndexInfo(true);
        $this->assertTrue($this->action->getIndexInfo());
    }
}
