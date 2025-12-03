<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestStatus;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class RequestStatusTest extends TestCase
{
    protected RequestStatus $action;

    public function setUp(): void
    {
        $this->action = new RequestStatus();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_REQUEST_STATUS, $this->action->getType());
    }

    public function testSetRequestId(): void
    {
        $this->action->setRequestId('myAsyncId');
        $this->assertSame('myAsyncId', $this->action->getRequestId());
    }
}
