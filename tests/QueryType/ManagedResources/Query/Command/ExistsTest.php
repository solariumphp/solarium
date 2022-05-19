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
     * Test workaround for SOLR-15116.
     *
     * Affected: Solr 8.7 – Solr 8.11.1, Solr 9.0
     * Fixed: Solr 8.11.2, Solr 9.1
     *
     * A HEAD request for a non-existing term against affected Solr versions returns "200 OK"
     * instead of "404 Not Found". We execute a GET request if a term is set as a workaround.
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
