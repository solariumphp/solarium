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

    public function testConstructor()
    {
        $exists = new Exists();
        $this->assertFalse($exists->getUseHeadRequest());

        $exists = new Exists(['useHeadRequest' => true]);
        $this->assertTrue($exists->getUseHeadRequest());
    }

    public function testConfigMode()
    {
        $options = [
            'useHeadRequest' => true,
        ];

        $this->assertFalse($this->exists->getUseHeadRequest());
        $this->exists->setOptions($options);
        $this->assertTrue($this->exists->getUseHeadRequest());
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_EXISTS, $this->exists->getType());
    }

    /**
     * GET requests are used by default to avoid SOLR-15116 and SOLR-16274.
     *
     * ==========
     * SOLR-15116
     * ==========
     *
     * Affected: Solr 8.7 – Solr 8.11.1, Solr 9.0
     * Fixed: Solr 8.11.2, Solr 9.1
     *
     * A HEAD request for a non-existing term against affected Solr versions returns "200 OK"
     * instead of "404 Not Found".
     *
     * ==========
     * SOLR-16274
     * ==========
     *
     * Affected: Solr 8.11.2, Solr 9.0 – 9.1
     * Fixed: Solr 8.11.3, Solr 9.2
     *
     * A HEAD request for an existing stopword list, synonym map, or term against affected
     * Solr versions returns "500 Server Error" instead of "200 OK".
     */
    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_GET, $this->exists->getRequestMethod());
    }

    /**
     * HEAD requests are only used when explicitly instructed to by the user.
     *
     * @testWith [true, "METHOD_HEAD"]
     *           [false, "METHOD_GET"]
     */
    public function testGetRequestMethodWithUseHeadRequest(bool $useHeadRequest, string $method)
    {
        $this->exists->setUseHeadRequest($useHeadRequest);
        $this->assertSame(\constant(Request::class.'::'.$method), $this->exists->getRequestMethod());
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

    public function testSetAndGetUseHeadRequest()
    {
        $this->assertFalse($this->exists->getUseHeadRequest());
        $this->exists->setUseHeadRequest(true);
        $this->assertTrue($this->exists->getUseHeadRequest());
    }
}
