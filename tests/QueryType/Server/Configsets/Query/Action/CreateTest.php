<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Create;

class CreateTest extends TestCase
{
    /**
     * @var Create
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Create();
    }

    public function testGetType()
    {
        $this->assertSame('CREATE', $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetBaseConfigSet()
    {
        $this->action->setBaseConfigSet('test');
        $this->assertSame('test', $this->action->getBaseConfigSet());
    }
}
