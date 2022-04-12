<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Delete;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;

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
        $this->assertSame(ConfigsetsQuery::ACTION_DELETE, $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testGetResultClass()
    {
        $this->assertSame(ConfigsetsResult::class, $this->action->getResultClass());
    }
}
