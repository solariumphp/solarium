<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Interval;
use Solarium\Component\FacetSet;

class IntervalTest extends TestCase
{
    /**
     * @var Interval
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new Interval();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'set' => ['i1', 'i2'],
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getLocalParameters()->getExcludes());
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
