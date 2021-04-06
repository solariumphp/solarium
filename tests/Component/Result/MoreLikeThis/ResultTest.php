<?php

namespace Solarium\Tests\Component\Result\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\QueryType\Select\Result\Document;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    private $mltResult;

    /**
     * @var array
     */
    private $docs;

    /**
     * @var array
     */
    private $interestingTerms;

    public function setUp(): void
    {
        $this->docs = [
            new Document(['id' => 1, 'name' => 'test1']),
            new Document(['id' => 2, 'name' => 'test2']),
        ];

        $this->interestingTerms = [
            'cat:term1' => 1.0,
            'cat:term2' => 1.84,
        ];

        $this->mltResult = new Result(2, 5.13, $this->docs, $this->interestingTerms);
    }

    public function testGetNumFound()
    {
        $this->assertEquals(2, $this->mltResult->getNumFound());
    }

    public function testGetMaximumScore()
    {
        $this->assertEquals(5.13, $this->mltResult->getMaximumScore());
    }

    public function testGetDocuments()
    {
        $this->assertEquals($this->docs, $this->mltResult->getDocuments());
    }

    public function testInterestingTerms()
    {
        $this->assertEquals($this->interestingTerms, $this->mltResult->getInterestingTerms());
    }

    public function testIterator()
    {
        $docs = [];
        foreach ($this->mltResult as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertEquals($this->docs, $docs);
    }

    public function testCount()
    {
        $this->assertCount(count($this->docs), $this->mltResult);
    }
}
