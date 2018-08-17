<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Swap;

class SwapTest extends TestCase
{
    /**
     * @var Swap
     */
    protected $action;

    public function setUp()
    {
        $this->action = new Swap();
    }

    public function testSetOther()
    {
        $this->action->setOther('targetCore');
        $this->assertSame('targetCore', $this->action->getOther());
    }

    public function testGetType()
    {
        $this->assertSame('SWAP', $this->action->getType());
    }
}
