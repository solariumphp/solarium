<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\MultiQuery;

class MultiQueryTest extends TestCase
{
    protected $values;

    protected $facet;

    public function setUp(): void
    {
        $this->values = [
            'a' => 12,
            'b' => 5,
            'c' => 3,
        ];
        $this->facet = new MultiQuery($this->values);
    }

    public function testGetValues()
    {
        $this->assertSame($this->values, $this->facet->getValues());
    }

    public function testCount()
    {
        $this->assertCount(count($this->values), $this->facet);
    }

    public function testIterator()
    {
        $values = [];
        foreach ($this->facet as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertSame($this->values, $values);
    }
}
