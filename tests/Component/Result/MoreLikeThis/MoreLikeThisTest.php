<?php

namespace Solarium\Tests\Component\Result\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Result\Document;

class MoreLikeThisTest extends TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    protected $results;

    protected $interestingTerms;

    public function setUp(): void
    {
        $docs = [
            new Document(['id' => 1, 'name' => 'test1']),
            new Document(['id' => 2, 'name' => 'test2']),
        ];

        $this->results = [
            'key1' => new Result(2, 5.13, $docs),
            'key2' => new Result(2, 2.3, $docs),
        ];

        $this->interestingTerms = [
            'key1' => ['cat:term1' => 1.0, 'cat:term2' => 1.84],
            'key2' => ['cat:term1' => 1.0, 'cat:term3' => 1.23],
        ];

        $this->mlt = new MoreLikeThis($this->results, $this->interestingTerms);
    }

    public function testGetResults()
    {
        $this->assertEquals($this->results, $this->mlt->getResults());
    }

    public function testGetResult()
    {
        $this->assertEquals(
            $this->results['key1'],
            $this->mlt->getResult('key1')
        );
    }

    public function testGetInvalidResult()
    {
        $this->assertNull(
            $this->mlt->getResult('invalid')
        );
    }

    public function testGetInterestingTerms()
    {
        $this->assertEquals($this->interestingTerms, $this->mlt->getInterestingTerms());
    }

    public function testGetInterestingTermsNone()
    {
        $mlt = new MoreLikeThis($this->results, null);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $mlt->getInterestingTerms();
    }

    public function testGetInterestingTerm()
    {
        $this->assertEquals(
            $this->interestingTerms['key1'],
            $this->mlt->getInterestingTerm('key1')
        );
    }

    public function testGetInvalidInterestingTerm()
    {
        $this->assertNull(
            $this->mlt->getInterestingTerm('invalid')
        );
    }

    public function testGetInterestingTermNone()
    {
        $mlt = new MoreLikeThis($this->results, null);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $mlt->getInterestingTerm('key1');
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->mlt as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->results, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->results), $this->mlt);
    }
}
