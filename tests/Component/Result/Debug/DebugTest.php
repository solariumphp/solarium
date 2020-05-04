<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\DocumentSet;
use Solarium\Component\Result\Debug\Result;
use Solarium\Component\Result\Debug\Timing;

class DebugTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $queryString;

    protected $queryParser;

    protected $parsedQuery;

    protected $otherQuery;

    protected $explain;

    protected $explainOther;

    protected $explainData;

    protected $timing;

    public function setUp(): void
    {
        $this->queryString = 'dummy-querystring';
        $this->parsedQuery = 'dummy-parsed-qs';
        $this->queryParser = 'dummy-parser';
        $this->otherQuery = 'id:67';
        $this->explainData = ['a' => 'dummy1', 'b' => 'dummy2'];
        $this->explain = new DocumentSet($this->explainData);
        $this->explainOther = new DocumentSet(['dummy-other']);
        $this->timing = new Timing(1.23, ['dummy-timing']);

        $this->result = new Result(
            $this->queryString,
            $this->parsedQuery,
            $this->queryParser,
            $this->otherQuery,
            $this->explain,
            $this->explainOther,
            $this->timing
        );
    }

    public function testGetQueryString()
    {
        $this->assertEquals($this->queryString, $this->result->getQueryString());
    }

    public function testGetParsedQuery()
    {
        $this->assertEquals($this->parsedQuery, $this->result->getParsedQuery());
    }

    public function testGetQueryParser()
    {
        $this->assertEquals($this->queryParser, $this->result->getQueryParser());
    }

    public function testGetOtherQuery()
    {
        $this->assertEquals($this->otherQuery, $this->result->getOtherQuery());
    }

    public function testGetExplain()
    {
        $this->assertEquals($this->explain, $this->result->getExplain());
    }

    public function testGetExplainOther()
    {
        $this->assertEquals($this->explainOther, $this->result->getExplainOther());
    }

    public function testGetTiming()
    {
        $this->assertEquals($this->timing, $this->result->getTiming());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->explainData, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->explain), $this->result);
    }
}
