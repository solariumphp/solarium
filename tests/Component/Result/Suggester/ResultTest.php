<?php

namespace Solarium\Tests\Component\Result\Suggester;

use Solarium\Component\Result\Suggester\Result;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    public function setUp()
    {
        $this->docs = array(
            'dictionary1' => new Dictionary([
                'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
                'zoo' => new Term(1, [['term' => 'zoo keeper']]),
            ]),
            'dictionary2' => new Dictionary([
                'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
            ]),
        );

        $all = array(
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        );


        $this->result = new Result($this->docs, $all);
    }

    public function testGetDictionary()
    {
         $this->assertEquals($this->docs['dictionary1'], $this->result->getDictionary('dictionary1'));
    }

    public function testIterator()
    {
        $docs = array();
        foreach ($this->result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertEquals($this->docs, $docs);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->docs), count($this->result));
    }
}
