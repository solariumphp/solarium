<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;

class ExistsTest extends TestCase
{
    /** @var Exists */
    protected $exists;

    public function setUp(): void
    {
        $this->exists = new Exists();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_EXISTS, $this->exists->getType());
    }

    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_HEAD, $this->exists->getRequestMethod());
    }

    /**
     * There's a bug since Solr 8.7 with HEAD requests if a term is set (SOLR-15116)
     */
    public function testGetRequestMethodWithTerm()
    {
        $this->exists->setTerm('test');
        $this->assertSame(Request::METHOD_GET, $this->exists->getRequestMethod());
    }

    public function testSetAndGetTerm()
    {
        $this->exists->setTerm('test');
        $this->assertSame('test', $this->exists->getTerm());
    }

    public function testRemoveTerm()
    {
        $this->exists->setTerm('test');
        $this->exists->removeTerm();
        $this->assertNull($this->exists->getTerm());
    }
}
