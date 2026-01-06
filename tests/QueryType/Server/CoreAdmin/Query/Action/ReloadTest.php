<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Reload;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class ReloadTest extends TestCase
{
    protected Reload $action;

    public function setUp(): void
    {
        $this->action = new Reload();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_RELOAD, $this->action->getType());
    }

    public function testSetCore(): void
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }
}
