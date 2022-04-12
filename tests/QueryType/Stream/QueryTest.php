<?php

namespace Solarium\Tests\QueryType\Stream;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Stream\Query;
use Solarium\QueryType\Stream\RequestBuilder;
use Solarium\QueryType\Stream\ResponseParser;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testConfigMode()
    {
        $q = new Query(['expr' => 'e1']);

        $this->assertSame('e1', $q->getExpression());
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_STREAM, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testSetAndGetExpression()
    {
        $this->query->setExpression('testexpression');
        $this->assertSame('testexpression', $this->query->getExpression());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('testdocumentclass');
        $this->assertSame('testdocumentclass', $this->query->getDocumentClass());
    }
}
