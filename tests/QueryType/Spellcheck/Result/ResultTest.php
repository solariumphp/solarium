<?php

namespace Solarium\Tests\QueryType\Spellcheck\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Spellcheck\Result\Result;

class ResultTest extends TestCase
{
    /**
     * @var SpellcheckDummy
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

    /**
     * @var string
     */
    protected $collation;

    public function setUp()
    {
        $this->data = [
            'term1' => 'data1',
            'term2' => 'data2',
        ];
        $this->allData = array_values($this->data);
        $this->collation = 'collation result';
        $this->result = new SpellcheckDummy($this->data, $this->allData, $this->collation);
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

    public function testGetTerm()
    {
        $this->assertSame($this->data['term1'], $this->result->getTerm('term1'));
    }

    public function testGetTermsWithInvalidFieldName()
    {
        $this->assertSame([], $this->result->getTerm('term3'));
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

    public function testGetCollation()
    {
        $this->assertSame($this->collation, $this->result->getCollation());
    }
}

class SpellcheckDummy extends Result
{
    protected $parsed = true;

    public function __construct($results, $all, $collation)
    {
        $this->results = $results;
        $this->all = $all;
        $this->collation = $collation;
        $this->status = 1;
        $this->queryTime = 12;
    }
}
