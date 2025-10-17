<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command\Stopwords;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Create;

class CreateTest extends TestCase
{
    /** @var Create */
    protected $create;

    public function setUp(): void
    {
        $this->create = new Create();
    }

    public function testGetType(): void
    {
        $this->assertSame(Query::COMMAND_CREATE, $this->create->getType());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertSame(Request::METHOD_PUT, $this->create->getRequestMethod());
    }

    public function testGetRawData(): void
    {
        $this->assertSame('{"class":"org.apache.solr.rest.schema.analysis.ManagedWordSetResource"}', $this->create->getRawData());
    }
}
