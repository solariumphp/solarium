<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Suggestion;

class SuggestionTest extends TestCase
{
    protected Suggestion $result;

    protected int $numFound;

    protected int $startOffset;

    protected int $endOffset;

    protected int $originalFrequency;

    protected array $words;

    protected string $originalTerm;

    public function setUp(): void
    {
        $this->numFound = 1;
        $this->startOffset = 2;
        $this->endOffset = 3;
        $this->originalFrequency = 4;
        $this->words = [
            [
                'word' => 'dummyword',
                'freq' => 5,
            ],
            [
                'word' => 'secondword',
                'freq' => 1,
            ],
        ];
        $this->originalTerm = 'wrongword';

        $this->result = new Suggestion(
            $this->numFound,
            $this->startOffset,
            $this->endOffset,
            $this->originalFrequency,
            $this->words,
            $this->originalTerm,
        );
    }

    public function testGetNumFound(): void
    {
        $this->assertEquals($this->numFound, $this->result->getNumFound());
    }

    public function testGetStartOffset(): void
    {
        $this->assertEquals($this->startOffset, $this->result->getStartOffset());
    }

    public function testGetEndOffset(): void
    {
        $this->assertEquals($this->endOffset, $this->result->getEndOffset());
    }

    public function testGetOriginalFrequency(): void
    {
        $this->assertEquals($this->originalFrequency, $this->result->getOriginalFrequency());
    }

    public function testGetWord(): void
    {
        $this->assertEquals($this->words[0]['word'], $this->result->getWord());
    }

    public function testGetFrequency(): void
    {
        $this->assertEquals($this->words[0]['freq'], $this->result->getFrequency());
    }

    public function testGetWords(): void
    {
        $this->assertEquals($this->words, $this->result->getWords());
    }

    public function testGetOriginalTerm(): void
    {
        $this->assertEquals($this->originalTerm, $this->result->getOriginalTerm());
    }
}
