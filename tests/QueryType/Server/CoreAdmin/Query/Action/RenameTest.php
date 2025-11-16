<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Rename;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class RenameTest extends TestCase
{
    protected Rename $action;

    public function setUp(): void
    {
        $this->action = new Rename();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_RENAME, $this->action->getType());
    }

    public function testSetCore(): void
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }

    public function testSetAsync(): void
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetOther(): void
    {
        $this->action->setOther('newName');
        $this->assertSame('newName', $this->action->getOther());
    }
}
