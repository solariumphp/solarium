<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\ListConfigsets;

class ListTest extends TestCase
{
    /**
     * @var ListConfigsets
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new ListConfigsets();
    }

    public function testGetType()
    {
        $this->assertSame('LIST', $this->action->getType());
    }
}
