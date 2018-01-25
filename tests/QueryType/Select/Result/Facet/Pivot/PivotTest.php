<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet\Pivot;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\Pivot\Pivot;
use Solarium\Component\Result\Facet\Pivot\PivotItem;

class PivotTest extends TestCase
{
    protected $values;

    protected $facet;

    public function setUp()
    {
        $this->values = [
            ['field' => 'cat', 'value' => 1, 'count' => 12],
            ['field' => 'cat', 'value' => 2, 'count' => 8],
        ];
        $this->facet = new Pivot($this->values);
    }

    public function testGetPivot()
    {
        $expected = [
            new PivotItem($this->values[0]),
            new PivotItem($this->values[1]),
        ];

        $this->assertEquals($expected, $this->facet->getPivot());
    }

    public function testCount()
    {
        $this->assertSame(count($this->values), count($this->facet));
    }
}
