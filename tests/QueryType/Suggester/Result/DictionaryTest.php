<?php

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

class DictionaryTest extends TestCase
{
    /**
     * @var Term[]
     */
    protected $terms;

    /**
     * @var Dictionary
     */
    protected $dictionary;

    public function setUp(): void
    {
        $this->terms = [
            'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            'zoo' => new Term(1, [['term' => 'zoo keeper']]),
        ];

        $this->dictionary = new Dictionary($this->terms);
    }

    public function testGetTerms()
    {
        $this->assertSame($this->terms, $this->dictionary->getTerms());
    }

    public function testGetTerm()
    {
        $this->assertSame($this->terms['zoo'], $this->dictionary->getTerm('zoo'));
    }

    public function testGetTermWithUnknownKey()
    {
        $this->assertNull($this->dictionary->getTerm('bar'));
    }

    public function testCount()
    {
        $this->assertCount(count($this->terms), $this->dictionary);
    }

    public function testIterator()
    {
        $results = [];
        foreach ($this->dictionary as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertSame($this->terms, $results);
    }
}
