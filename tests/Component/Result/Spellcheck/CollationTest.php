<?php

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Collation;

class CollationTest extends TestCase
{
    /**
     * @var Collation
     */
    protected $result;

    protected $corrections;

    protected $hits;

    protected $query;

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

    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->result->getQuery());
    }

    public function testGetHits()
    {
        $this->assertEquals($this->hits, $this->result->getHits());
    }

    public function testGetCorrections()
    {
        $this->assertEquals($this->corrections, $this->result->getCorrections());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->corrections, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->corrections), $this->result);
    }
}
