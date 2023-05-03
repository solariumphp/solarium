<?php

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;
use Solarium\QueryType\Suggester\Result\Term;

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

    public function setUp(): void
    {
        $this->data = [
            'dictionary1' => new Dictionary([
                'term1' => new Term(1, [['term' => 'data1']]),
                'term2' => new Term(1, [['term' => 'data2']]),
            ]),
            'dictionary2' => new Dictionary([
                'term3' => new Term(1, [['term' => 'data3']]),
            ]),
        ];
        $this->allData = [
            new Term(1, [['term' => 'data1']]),
            new Term(1, [['term' => 'data2']]),
            new Term(1, [['term' => 'data3']]),
        ];

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
        $this->assertSame('data1', $dictionary->getTerm('term1')->getSuggestions()[0]['term']);
    }

    public function testGetDictionaryWithInvalidFieldName()
    {
        $this->assertNull($this->result->getDictionary('dictionary3'));
    }

    public function testCount()
    {
        $this->assertCount(count($this->data), $this->result);
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
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
