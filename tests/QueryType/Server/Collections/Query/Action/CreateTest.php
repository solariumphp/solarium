<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\Create;

class CreateTest extends TestCase
{
    /**
     * @var Create
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Create();
    }

    public function testSetCore()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testGetType()
    {
        $this->assertSame('CREATE', $this->action->getType());
    }
}
