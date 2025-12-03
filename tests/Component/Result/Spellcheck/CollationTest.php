<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Collation;

class CollationTest extends TestCase
{
    protected Collation $result;

    protected array $corrections;

    protected int $hits;

    protected string $query;

    public function setUp(): void
    {
        $this->corrections = [
            'key1' => 'content1',
            'key2' => 'content2',
        ];
        $this->hits = 1;
        $this->query = 'dummy query';

        $this->result = new Collation($this->query, $this->hits, $this->corrections);
    }

    public function testGetQuery(): void
    {
        $this->assertEquals($this->query, $this->result->getQuery());
    }

    public function testGetHits(): void
    {
        $this->assertEquals($this->hits, $this->result->getHits());
    }

    public function testGetCorrections(): void
    {
        $this->assertEquals($this->corrections, $this->result->getCorrections());
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->corrections, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->corrections, $this->result);
    }
}
