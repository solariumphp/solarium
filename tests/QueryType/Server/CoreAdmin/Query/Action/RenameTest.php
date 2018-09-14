<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Rename;

class RenameTest extends TestCase
{
    /**
     * @var Rename
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Rename();
    }

    public function testSetOther()
    {
        $this->action->setOther('newName');
        $this->assertSame('newName', $this->action->getOther());
    }

    public function testGetType()
    {
        $this->assertSame('RENAME', $this->action->getType());
    }
}
