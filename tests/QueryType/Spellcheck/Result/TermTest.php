<?php

namespace Solarium\Tests\QueryType\Spellcheck\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Spellcheck\Result\Term;

class TermTest extends TestCase
{
    /**
     * @var Term
     */
    protected $result;

    /**
     * @var int
     */
    protected $numFound;

    /**
     * @var int
     */
    protected $startOffset;

    /**
     * @var int
     */
    protected $endOffset;

    /**
     * @var array
     */
    protected $suggestions;

    public function setUp(): void
    {
        $this->numFound = 5;
        $this->startOffset = 2;
        $this->endOffset = 6;
        $this->suggestions = [
            'suggestion1',
            'suggestion2',
        ];

        $this->result = new Term($this->numFound, $this->startOffset, $this->endOffset, $this->suggestions);
    }

    public function testGetNumFound()
    {
        $this->assertSame(
            $this->numFound,
            $this->result->getNumFound()
        );
    }

    public function testGetStartOffset()
    {
        $this->assertSame(
            $this->startOffset,
            $this->result->getStartOffset()
        );
    }

    public function testGetEndOffset()
    {
        $this->assertSame(
            $this->endOffset,
            $this->result->getEndOffset()
        );
    }

    public function testGetSuggestions()
    {
        $this->assertSame(
            $this->suggestions,
            $this->result->getSuggestions()
        );
    }

    public function testCount()
    {
        $this->assertCount(count($this->suggestions), $this->result);
    }

    public function testIterator()
    {
        $results = [];
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertSame($this->suggestions, $results);
    }
}
