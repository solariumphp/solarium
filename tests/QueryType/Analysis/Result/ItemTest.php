<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\Item;

class ItemTest extends TestCase
{
    /**
     * @var Item
     */
    protected $item;

    protected $data;

    public function setUp(): void
    {
        $this->data = [
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => [2, 1],
            'type' => '<dummytype>',
            'raw_text' => 'dummy raw text',
            'match' => true,
        ];
        $this->item = new Item($this->data);
    }

    public function testGetText()
    {
        $this->assertSame($this->data['text'], $this->item->getText());
    }

    public function testGetStart()
    {
        $this->assertSame($this->data['start'], $this->item->getStart());
    }

    public function testGetEnd()
    {
        $this->assertSame($this->data['end'], $this->item->getEnd());
    }

    public function testGetPosition()
    {
        $this->assertSame($this->data['position'], $this->item->getPosition());
    }

    public function testGetPositionHistory()
    {
        $this->assertSame($this->data['positionHistory'], $this->item->getPositionHistory());
    }

    public function testGetPositionHistoryFallbackValue()
    {
        $data = $this->data;
        $data['positionHistory'] = '';
        $item = new Item($data);
        $this->assertSame([], $item->getPositionHistory());
    }

    public function testGetRawText()
    {
        $this->assertSame($this->data['raw_text'], $this->item->getRawText());
    }

    public function testGetType()
    {
        $this->assertSame($this->data['type'], $this->item->getType());
    }

    public function testGetRawTextEmpty()
    {
        $data = [
            'text' => 'dummytest',
            'start' => 10,
            'end' => 22,
            'position' => 2,
            'positionHistory' => [2, 1],
            'type' => '<dummytype>',
        ];
        $item = new Item($data);
        $this->assertNull($item->getRawText());
    }

    public function testGetMatch()
    {
        $this->assertSame($this->data['match'], $this->item->getMatch());
    }
}
