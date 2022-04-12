<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\FacetSet;

class JsonAggregationTest extends TestCase
{
    /**
     * @var JsonAggregation
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new JsonAggregation();
    }

    public function testConfigMode()
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

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::JSON_FACET_AGGREGATION,
            $this->facet->getType()
        );
    }

    public function testSetAndGetFunction()
    {
        $this->facet->setFunction('sum(field)');
        $this->assertSame('sum(field)', $this->facet->getFunction());
    }

    public function testSetAndGetMin()
    {
        $this->facet->setMin(100);
        $this->assertSame(100, $this->facet->getMin());
    }
}
