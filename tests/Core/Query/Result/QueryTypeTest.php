<?php

namespace Solarium\Tests\Core\Query\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\QueryType as QueryTypeResult;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

class QueryTypeTest extends TestCase
{
    /**
     * @var TestStubResult
     */
    protected $result;

    public function setUp(): void
    {
        $query = new UpdateQuery();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP 1.1 200 OK']);
        $this->result = new TestStubResult($query, $response);
    }

    public function testGetStatus()
    {
        $this->assertSame(1, $this->result->getStatus());
    }

    public function testGetQueryTime()
    {
        $this->assertSame(12, $this->result->getQueryTime());
    }

    public function testParseResponse()
    {
        $query = new TestStubQuery();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP 1.1 200 OK']);
        $result = new TestStubResult($query, $response);

        $this->expectException(UnexpectedValueException::class);
        $result->parse();
    }

    public function testParseResponseInvalidQuerytype()
    {
        $this->assertNull($this->result->parse());
    }

    public function testParseResponseResponseHeaderFallback()
    {
        $query = new SelectQuery();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP 1.1 200 OK']);
        $result = new TestNonDataMappingStubResult($query, $response);

        $this->assertSame(1, $result->getStatus());
        $this->assertSame(12, $result->getQueryTime());
    }

    public function testParseLazyLoading()
    {
        $this->assertSame(0, $this->result->parseCount);

        $this->result->parse();
        $this->assertSame(1, $this->result->parseCount);

        $this->result->parse();
        $this->assertSame(1, $this->result->parseCount);
    }

    public function testMapData()
    {
        $this->result->mapData(['dummyvar' => 'dummyvalue']);

        $this->assertSame('dummyvalue', $this->result->getVar('dummyvar'));
    }
}

class TestStubQuery extends SelectQuery
{
    public function getType(): string
    {
        return 'dummy';
    }

    public function getResponseParser(): ?ResponseParserInterface
    {
        return null;
    }
}

class TestStubResult extends QueryTypeResult
{
    public $parseCount = 0;

    protected $dummyvar;

    public function parse()
    {
        $this->parseResponse();
    }

    public function mapData(array $data)
    {
        ++$this->parseCount;
        parent::mapData($data);
    }

    public function getVar($name)
    {
        return $this->$name;
    }
}

class TestNonDataMappingStubResult extends QueryTypeResult
{
    protected function mapData(array $mapData)
    {
    }
}
