<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Suggestion;

class SuggestionTest extends TestCase
{
    /**
     * @var Suggestion
     */
    protected $result;

    protected $numFound;

    protected $startOffset;

    protected $endOffset;

    protected $originalFrequency;

    protected $words;

    protected $frequency;

    public function setUp()
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

        $this->result = new Suggestion(
            $this->numFound,
            $this->startOffset,
            $this->endOffset,
            $this->originalFrequency,
            $this->words
        );
    }

    public function testGetNumFound()
    {
        $this->assertEquals($this->numFound, $this->result->getNumFound());
    }

    public function testGetStartOffset()
    {
        $this->assertEquals($this->startOffset, $this->result->getStartOffset());
    }

    public function testGetEndOffset()
    {
        $this->assertEquals($this->endOffset, $this->result->getEndOffset());
    }

    public function testGetOriginalFrequency()
    {
        $this->assertEquals($this->originalFrequency, $this->result->getOriginalFrequency());
    }

    public function testGetWord()
    {
        $this->assertEquals($this->words[0]['word'], $this->result->getWord());
    }

    public function testGetFrequency()
    {
        $this->assertEquals($this->words[0]['freq'], $this->result->getFrequency());
    }

    public function testGetWords()
    {
        $this->assertEquals($this->words, $this->result->getWords());
    }
}
