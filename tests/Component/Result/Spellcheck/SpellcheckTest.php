<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Result;

class SpellcheckTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $suggestions;

    protected $collations;

    protected $correctlySpelled;

    public function setUp()
    {
        $this->suggestions = [
            'key1' => 'content1',
            'key2' => 'content2',
        ];

        $this->collations = [
            'dummy1',
            'dummy2',
        ];
        $this->correctlySpelled = false;

        $this->result = new Result($this->suggestions, $this->collations, $this->correctlySpelled);
    }

    public function testGetCollation()
    {
        $this->assertEquals(reset($this->collations), $this->result->getCollation());
    }

    public function testGetCollationWithoutData()
    {
        $result = new Result($this->suggestions, [], $this->correctlySpelled);
        $this->assertNull($result->getCollation());
    }

    public function testGetCollationWithKey()
    {
        $this->assertEquals($this->collations[0], $this->result->getCollation(0));
    }

    public function testGetCollations()
    {
        $this->assertEquals($this->collations, $this->result->getCollations());
    }

    public function testGetCorrectlySpelled()
    {
        $this->assertEquals($this->correctlySpelled, $this->result->getCorrectlySpelled());
    }

    public function testGetSuggestion()
    {
        $this->assertEquals($this->suggestions['key1'], $this->result->getSuggestion('key1'));
    }

    public function testGetInvalidSuggestion()
    {
        $this->assertNull($this->result->getSuggestion('key3'));
    }

    public function testGetSuggestions()
    {
        $this->assertEquals($this->suggestions, $this->result->getSuggestions());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->suggestions, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->suggestions), count($this->result));
    }
}
