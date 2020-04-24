<?php

namespace Solarium\Tests\Component\Result\Suggester;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Suggester\Result;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    private $result;

    /**
     * @var array
     */
    private $docs;

    public function setUp(): void
    {
        $this->docs = [
            'dictionary1' => new Dictionary([
                'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
                'zoo' => new Term(1, [['term' => 'zoo keeper']]),
            ]),
            'dictionary2' => new Dictionary([
                'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
            ]),
        ];

        $all = [
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        ];

        $this->result = new Result($this->docs, $all);
    }

    public function testGetDictionary()
    {
        $this->assertEquals($this->docs['dictionary1'], $this->result->getDictionary('dictionary1'));
    }

    public function testIterator()
    {
        $docs = [];
        foreach ($this->result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertEquals($this->docs, $docs);
    }

    public function testCount()
    {
        $this->assertCount(count($this->docs), $this->result);
    }
}
