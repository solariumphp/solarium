<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Swap;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class SwapTest extends TestCase
{
    /**
     * @var Swap
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Swap();
    }

    public function testGetType()
    {
        $this->assertSame(CoreAdminQuery::ACTION_SWAP, $this->action->getType());
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

    public function testSetOther()
    {
        $this->action->setOther('targetCore');
        $this->assertSame('targetCore', $this->action->getOther());
    }
}
