<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\Aggregation;

class AggregationTest extends TestCase
{
    public function testGetValueFloat(): void
    {
        $facet = new Aggregation(3.14);

        $this->assertSame(3.14, $facet->getValue());
    }

    public function testGetValueInt(): void
    {
        $facet = new Aggregation(42);

        $this->assertSame(42, $facet->getValue());
    }
}
