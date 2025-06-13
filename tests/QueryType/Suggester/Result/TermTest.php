<?php

namespace Solarium\Tests\QueryType\Suggester\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Result\Term;

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
     * @var array
     */
    protected $suggestions;

    public function setUp(): void
    {
        $this->numFound = 5;
        $this->suggestions = [
            'suggestion1',
            'suggestion2',
        ];

        $this->result = new Term($this->numFound, $this->suggestions);
    }

    public function testGetNumFound(): void
    {
        $this->assertSame(
            $this->numFound,
            $this->result->getNumFound()
        );
    }

    public function testGetSuggestions(): void
    {
        $this->assertSame(
            $this->suggestions,
            $this->result->getSuggestions()
        );
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->suggestions, $this->result);
    }

    public function testIterator(): void
    {
        $results = [];
        foreach ($this->result as $key => $doc) {
            $results[$key] = $doc;
        }

        $this->assertSame($this->suggestions, $results);
    }
}
