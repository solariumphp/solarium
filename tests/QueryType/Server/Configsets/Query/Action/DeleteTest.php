<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Delete();
    }

    public function testGetType()
    {
        $this->assertSame('DELETE', $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }
}
