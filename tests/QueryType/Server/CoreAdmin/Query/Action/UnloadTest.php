<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Unload;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class UnloadTest extends TestCase
{
    /**
     * @var Unload
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Unload();
    }

    public function testGetType()
    {
        $this->assertSame(CoreAdminQuery::ACTION_UNLOAD, $this->action->getType());
    }

    public function testSetCore()
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetDeleteIndex()
    {
        $this->action->setDeleteIndex(true);
        $this->assertTrue($this->action->getDeleteIndex());
    }

    public function testSetDeleteDataDir()
    {
        $this->action->setDeleteDataDir(true);
        $this->assertTrue($this->action->getDeleteDataDir());
    }

    public function testSetDeleteInstanceDir()
    {
        $this->action->setDeleteInstanceDir(true);
        $this->assertTrue($this->action->getDeleteInstanceDir());
    }
}
