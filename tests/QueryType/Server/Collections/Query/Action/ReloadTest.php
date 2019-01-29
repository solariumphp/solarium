<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\Reload;

class ReloadTest extends TestCase
{
    /**
     * @var Reload
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Reload();
    }

    public function testGetType()
    {
        $this->assertSame('RELOAD', $this->action->getType());
    }
}
