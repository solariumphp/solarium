<?php

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;

class ResultTest extends TestCase
{
    /**
     * @var SuggesterDummy
     */
    protected $result;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $allData;

    public function setUp()
    {
        $this->data = [
            'dictionary1' => new Dictionary([
                'term1' => 'data1',
                'term2' => 'data2',
            ]),
            'dictionary2' => new Dictionary([
                'term3' => 'data3',
            ]),
        ];
        $this->allData = ['data1', 'data2', 'data3'];
        $this->result = new SuggesterDummy($this->data, $this->allData);
    }

    public function testGetStatus()
    {
        $this->assertSame(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime()
    {
        $this->assertSame(
            12,
            $this->result->getQueryTime()
        );
    }

    public function testGetResults()
    {
        $this->assertSame($this->data, $this->result->getResults());
    }

    public function testGetAll()
    {
        $this->assertSame($this->allData, $this->result->getAll());
    }

    public function testGetDictionary()
    {
        $dictionary = $this->result->getDictionary('dictionary1');
        $this->assertSame('data1', $dictionary->getTerm('term1'));
    }

    public function testGetDictionaryWithInvalidFieldName()
    {
        $this->assertNull($this->result->getDictionary('dictionary3'));
    }

    public function testCount()
    {
        $this->assertSame(count($this->data), count($this->result));
    }

    public function testIterator()
    {
        $results = [];
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertSame($this->data, $results);
    }
}

class SuggesterDummy extends Result
{
    protected $parsed = true;

    public function __construct($results, $all)
    {
        $this->results = $results;
        $this->all = $all;
        $this->status = 1;
        $this->queryTime = 12;
    }
}
