<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestStatus;

class RequestStatusTest extends TestCase
{
    /**
     * @var RequestStatus
     */
    protected $action;

    public function setUp()
    {
        $this->action = new RequestStatus();
    }

    public function testSetRequestId()
    {
        $this->action->setRequestId('myAsyncId');
        $this->assertSame('myAsyncId', $this->action->getRequestId());
    }

    public function testGetType()
    {
        $this->assertSame('REQUESTSTATUS', $this->action->getType());
    }
}
