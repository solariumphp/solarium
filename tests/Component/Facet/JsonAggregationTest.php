<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\FacetSet;

class JsonAggregationTest extends TestCase
{
    protected JsonAggregation $facet;

    public function setUp(): void
    {
        $this->facet = new JsonAggregation();
    }

    public function testConfigMode(): void
    {
        $options = [
            'local_key' => 'myKey',
            'function' => 'unique(field)',
            'min' => 5,
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['function'], $this->facet->getFunction());
        $this->assertSame($options['min'], $this->facet->getMin());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            FacetSet::JSON_FACET_AGGREGATION,
            $this->facet->getType()
        );
    }

    public function testSetAndGetFunction(): void
    {
        $this->facet->setFunction('sum(field)');
        $this->assertSame('sum(field)', $this->facet->getFunction());
    }

    public function testSetAndGetMin(): void
    {
        $this->facet->setMin(100);
        $this->assertSame(100, $this->facet->getMin());
    }
}
