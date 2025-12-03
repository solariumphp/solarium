<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Collation;
use Solarium\Component\Result\Spellcheck\Result;
use Solarium\Component\Result\Spellcheck\Suggestion;

class SpellcheckTest extends TestCase
{
    protected Result $result;

    /**
     * @var Suggestion[]
     */
    protected array $suggestions;

    /**
     * @var Collation[]
     */
    protected array $collations;

    protected bool $correctlySpelled;

    public function setUp(): void
    {
        $this->suggestions = [
            'key1' => new Suggestion(1, 2, 3, 4, ['content1']),
            'key2' => new Suggestion(1, 2, 3, 4, ['content2']),
        ];

        $this->collations = [
            new Collation('dummy1', null, []),
            new Collation('dummy2', null, []),
        ];
        $this->correctlySpelled = false;

        $this->result = new Result($this->suggestions, $this->collations, $this->correctlySpelled);
    }

    public function testGetCollation(): void
    {
        $this->assertEquals(reset($this->collations), $this->result->getCollation());
    }

    public function testGetCollationWithoutData(): void
    {
        $result = new Result($this->suggestions, [], $this->correctlySpelled);
        $this->assertNull($result->getCollation());
    }

    public function testGetCollationWithKey(): void
    {
        $this->assertEquals($this->collations[0], $this->result->getCollation(0));
    }

    public function testGetCollations(): void
    {
        $this->assertEquals($this->collations, $this->result->getCollations());
    }

    public function testGetCorrectlySpelled(): void
    {
        $this->assertEquals($this->correctlySpelled, $this->result->getCorrectlySpelled());
    }

    public function testGetSuggestion(): void
    {
        $this->assertEquals($this->suggestions['key1'], $this->result->getSuggestion('key1'));
    }

    public function testGetInvalidSuggestion(): void
    {
        $this->assertNull($this->result->getSuggestion('key3'));
    }

    public function testGetSuggestions(): void
    {
        $this->assertEquals($this->suggestions, $this->result->getSuggestions());
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->suggestions, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->suggestions, $this->result);
    }
}
