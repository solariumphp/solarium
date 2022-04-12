<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\ListConfigsets;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ListConfigsetsResult;

class ListConfigsetsTest extends TestCase
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
        $this->assertSame(ConfigsetsQuery::ACTION_LIST, $this->action->getType());
    }

    public function testGetResultClass()
    {
        $this->assertSame(ListConfigsetsResult::class, $this->action->getResultClass());
    }
}
