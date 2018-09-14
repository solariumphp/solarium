<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Unload;

class UnloadTest extends TestCase
{
    /**
     * @var Unload
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Unload();
    }

    public function testSetDeleteDataDir()
    {
        $this->action->setDeleteDataDir(true);
        $this->assertTrue($this->action->getDeleteDataDir());
    }

    public function testSetDeleteIndex()
    {
        $this->action->setDeleteIndex(true);
        $this->assertTrue($this->action->getDeleteIndex());
    }

    public function testSetDeleteInstanceDir()
    {
        $this->action->setDeleteInstanceDir(true);
        $this->assertTrue($this->action->getDeleteInstanceDir());
    }

    public function testGetType()
    {
        $this->assertSame('UNLOAD', $this->action->getType());
    }
}
