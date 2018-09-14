<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Create;

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
        $this->action->setCore('myCore');
        $this->assertSame('myCore', $this->action->getCore());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testGetType()
    {
        $this->assertSame('CREATE', $this->action->getType());
    }
}
