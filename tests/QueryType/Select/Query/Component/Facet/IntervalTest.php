<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Interval;
use Solarium\Component\FacetSet;

class IntervalTest extends TestCase
{
    /**
     * @var Interval
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Interval();
    }

    public function testConfigMode()
    {
        $options = [
            'key' => 'myKey',
            'exclude' => ['e1', 'e2'],
            'set' => ['i1', 'i2'],
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['key'], $this->facet->getKey());
        $this->assertSame($options['exclude'], $this->facet->getExcludes());
        $this->assertSame($options['set'], $this->facet->getSet());
    }

    public function testGetType()
    {
        $this->assertSame(FacetSet::FACET_INTERVAL, $this->facet->getType());
    }

    public function testSetAndGetSet()
    {
        $this->facet->setSet('interval1,interval2');
        $this->assertEquals(['interval1', 'interval2'], $this->facet->getSet());
    }

    public function testEmptySet()
    {
        $this->assertEquals([], $this->facet->getSet());
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('field1');
        $this->assertSame('field1', $this->facet->getField());
    }
}
