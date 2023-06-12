<?php

namespace Solarium\Tests\QueryType\Terms;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Terms\Result;

class ResultTest extends TestCase
{
    /**
     * @var TermsDummy
     */
    protected $result;

    /**
     * @var array
     */
    protected $data;

    public function setUp(): void
    {
        $this->data = [
            'fieldA' => [
                'term1',
                11,
                'term2',
                5,
                'term3',
                2,
            ],
            'fieldB' => [
                'term4',
                4,
                'term5',
                1,
            ],
        ];

        $this->result = new TermsDummy($this->data);
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

    public function testGetTerms()
    {
        $this->assertSame($this->data['fieldA'], $this->result->getTerms('fieldA'));
    }

    public function testGetTermsWithInvalidFieldName()
    {
        $this->assertSame([], $this->result->getTerms('fieldX'));
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

class TermsDummy extends Result
{
    protected $parsed = true;

    public function __construct($results)
    {
        $this->results = $results;
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
