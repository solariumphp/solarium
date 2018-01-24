<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet\Pivot;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\Pivot\PivotItem;

class PivotItemTest extends TestCase
{
    protected $values;

    protected $pivotItem;

    public function setUp()
    {
        $this->values = [
            'field' => 'cat',
            'value' => 'abc',
            'count' => '123',
            'pivot' => [
                ['field' => 'cat', 'value' => 1, 'count' => 12],
                ['field' => 'cat', 'value' => 2, 'count' => 8],
            ],
        ];
        $this->pivotItem = new PivotItem($this->values);
    }

    public function testGetField()
    {
        $this->assertSame($this->values['field'], $this->pivotItem->getField());
    }

    public function testGetValue()
    {
        $this->assertSame($this->values['value'], $this->pivotItem->getValue());
    }

    public function testGetCount()
    {
        $this->assertSame($this->values['count'], $this->pivotItem->getCount());
    }

    public function testCount()
    {
        $this->assertSame(count($this->values['pivot']), count($this->pivotItem));
    }
}
