<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;

class DeleteTest extends TestCase
{
    /** @var Delete */
    protected $delete;

    public function setUp(): void
    {
        $this->delete = new Delete();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_DELETE, $this->delete->getType());
    }

    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_DELETE, $this->delete->getRequestMethod());
    }

    public function testSetAndGetTerm()
    {
        $this->delete->setTerm('test');
        $this->assertSame('test', $this->delete->getTerm());
    }
}
