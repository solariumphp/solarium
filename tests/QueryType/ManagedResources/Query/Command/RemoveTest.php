<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;

class RemoveTest extends TestCase
{
    protected Remove $remove;

    public function setUp(): void
    {
        $this->remove = new Remove();
    }

    public function testGetType(): void
    {
        $this->assertSame(Query::COMMAND_REMOVE, $this->remove->getType());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertSame(Request::METHOD_DELETE, $this->remove->getRequestMethod());
    }
}
