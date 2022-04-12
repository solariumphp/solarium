<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Collections\Query\Action\Delete;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\DeleteResult;

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
        $this->assertSame(CollectionsQuery::ACTION_DELETE, $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testGetResultClass()
    {
        $this->assertSame(DeleteResult::class, $this->action->getResultClass());
    }
}
