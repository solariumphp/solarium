<?php

namespace Solarium\Tests\Component\Result\TermVector;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\TermVector\Term;

class TermTest extends TestCase
{
    /**
     * @var Term
     */
    protected $term;

    public function setUp(): void
    {
        $term = 'term1';
        $tf = 1;
        $positions = [5, 10];
        $offsets = [['start' => 15, 'end' => 20], ['start' => 25, 'end' => 30]];
        $payloads = ['Zmlyc3QgcGF5bG9hZA==', 'c2Vjb25kIHBheWxvYWQ='];
        $df = 4;
        $tfIdf = 0.25;

        $this->term = new Term($term, $tf, $positions, $offsets, $payloads, $df, $tfIdf);
    }

    public function testGetTerm()
    {
        $this->assertSame('term1', $this->term->getTerm());
    }

    public function testGetTermFrequency()
    {
        $this->assertSame(1, $this->term->getTermFrequency());
    }

    public function testGetPositions()
    {
        $this->assertSame([5, 10], $this->term->getPositions());
    }

    public function testGetOffsets()
    {
        $this->assertSame([['start' => 15, 'end' => 20], ['start' => 25, 'end' => 30]], $this->term->getOffsets());
    }

    public function testGetPayloads()
    {
        $this->assertSame(['Zmlyc3QgcGF5bG9hZA==', 'c2Vjb25kIHBheWxvYWQ='], $this->term->getPayloads());
    }

    public function testGetDocumentFrequency()
    {
        $this->assertSame(4, $this->term->getDocumentFrequency());
    }

    public function testGetTermFreqInverseDocFreq()
    {
        $this->assertSame(0.25, $this->term->getTermFreqInverseDocFreq());
    }

    public function testGetMissingInfoReturnsNull()
    {
        $term = new Term('term1', null, null, null, null, null, null);

        $this->assertNull($term->getTermFrequency());
        $this->assertNull($term->getPositions());
        $this->assertNull($term->getOffsets());
        $this->assertNull($term->getPayloads());
        $this->assertNull($term->getDocumentFrequency());
        $this->assertNull($term->getTermFreqInverseDocFreq());
    }

    /**
     * @testWith ["tf"]
     *           ["positions"]
     *           ["offsets"]
     *           ["payloads"]
     *           ["df"]
     *           ["tf-idf"]
     */
    public function testOffsetExists(string $offset)
    {
        $this->assertTrue($this->term->offsetExists($offset));
    }

    public function testOffsetExistsUnknown()
    {
        $this->assertFalse($this->term->offsetExists('unknown'));
    }

    /**
     * @testWith ["tf", 1]
     *           ["positions", [5, 10]]
     *           ["offsets", [{"start": 15, "end": 20}, {"start": 25, "end": 30}]]
     *           ["payloads", ["Zmlyc3QgcGF5bG9hZA==", "c2Vjb25kIHBheWxvYWQ="]]
     *           ["df", 4]
     *           ["tf-idf", 0.25]
     */
    public function testOffsetGet(string $offset, mixed $expected)
    {
        $this->assertSame($expected, $this->term->offsetGet($offset));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined property');
        $this->term->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->term->offsetSet('tf', 2);
        $this->assertSame(1, $this->term['tf']);
    }

    public function testOffsetUnsetImmutable()
    {
        $this->term->offsetUnset('tf');
        $this->assertSame(1, $this->term['tf']);
    }
}
