<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestRecovery;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class RequestRecoveryTest extends TestCase
{
    protected RequestRecovery $action;

    public function setUp(): void
    {
        $this->action = new RequestRecovery();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_REQUEST_RECOVERY, $this->action->getType());
    }

    public function testSetCore(): void
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }
}
