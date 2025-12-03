<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\MultiQuery;

class MultiQueryTest extends TestCase
{
    protected array $values;

    protected MultiQuery $facet;

    public function setUp(): void
    {
        $this->values = [
            'a' => 12,
            'b' => 5,
            'c' => 3,
        ];
        $this->facet = new MultiQuery($this->values);
    }

    public function testGetValues(): void
    {
        $this->assertSame($this->values, $this->facet->getValues());
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->values, $this->facet);
    }

    public function testIterator(): void
    {
        $values = [];
        foreach ($this->facet as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertSame($this->values, $values);
    }
}
