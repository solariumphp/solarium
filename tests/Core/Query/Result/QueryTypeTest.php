<?php

namespace Solarium\Tests\Core\Query\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
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

    public function setUp()
    {
        $query = new UpdateQuery();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP 1.1 200 OK']);
        $this->result = new TestStubResult($query, $response);
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

    public function getResponseParser()
    {
        return null;
    }
}

class TestStubResult extends QueryTypeResult
{
    public $parseCount = 0;

    public function parse()
    {
        $this->parseResponse();
    }

    public function mapData($data)
    {
        ++$this->parseCount;
        parent::mapData($data);
    }

    public function getVar($name)
    {
        return $this->$name;
    }
}
