<?php

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;
use Solarium\QueryType\Suggester\Result\Term;

class ResultTest extends TestCase
{
    protected SuggesterDummy $result;

    /**
     * @var Dictionary[]
     */
    protected array $data;

    /**
     * @var Term[]
     */
    protected array $allData;

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

    public function testGetStatus(): void
    {
        $this->assertSame(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime(): void
    {
        $this->assertSame(
            12,
            $this->result->getQueryTime()
        );
    }

    public function testGetResults(): void
    {
        $this->assertSame($this->data, $this->result->getResults());
    }

    public function testGetAll(): void
    {
        $this->assertSame($this->allData, $this->result->getAll());
    }

    public function testGetDictionary(): void
    {
        $dictionary = $this->result->getDictionary('dictionary1');
        $this->assertSame('data1', $dictionary->getTerm('term1')->getSuggestions()[0]['term']);
    }

    public function testGetDictionaryWithInvalidFieldName(): void
    {
        $this->assertNull($this->result->getDictionary('dictionary3'));
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->data, $this->result);
    }

    public function testIterator(): void
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
    protected bool $parsed = true;

    public function __construct($results, $all)
    {
        $this->results = $results;
        $this->all = $all;
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
