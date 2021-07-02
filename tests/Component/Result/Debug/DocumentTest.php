<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Document;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    protected $result;

    protected $value;

    protected $match;

    protected $description;

    protected $key;

    protected $details;

    public function setUp(): void
    {
        $this->key = 'dummy-key';
        $this->value = 1.5;
        $this->match = true;
        $this->description = 'dummy-desc';
        $this->details = [0 => 'dummy1', 1 => 'dummy2'];

        $this->result = new Document(
            $this->key,
            $this->match,
            $this->value,
            $this->description,
            $this->details
        );
    }

    public function testGetKey()
    {
        $this->assertEquals($this->key, $this->result->getKey());
    }

    public function testGetDetails()
    {
        $this->assertEquals($this->details, $this->result->getDetails());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->details, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->details), $this->result);
    }

    public function testToString()
    {
        $expected = '  dummy1'.PHP_EOL.'  dummy2'.PHP_EOL;

        $this->assertSame($expected, (string) $this->result);
    }
}
