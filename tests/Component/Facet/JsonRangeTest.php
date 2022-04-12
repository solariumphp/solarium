<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonRange;
use Solarium\Component\FacetSet;

class JsonRangeTest extends TestCase
{
    /**
     * @var JsonRange
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new JsonRange();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'field' => 'content',
            'start' => 1,
            'end' => 100,
            'gap' => 10,
            'hardend' => true,
            'other' => 'all',
            'include' => 'lower',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame((string) $options['start'], $this->facet->getStart());
        $this->assertSame((string) $options['end'], $this->facet->getEnd());
        $this->assertSame((string) $options['gap'], $this->facet->getGap());
        $this->assertTrue($this->facet->getHardend());
        $this->assertSame([$options['other']], $this->facet->getOther());
        $this->assertSame([$options['include']], $this->facet->getInclude());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::JSON_FACET_RANGE,
            $this->facet->getType()
        );
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('price');
        $this->assertSame('price', $this->facet->getField());
    }

    public function testSetAndGetStart()
    {
        $this->facet->setStart(1);
        $this->assertSame('1', $this->facet->getStart());
        $this->facet->setStart('1.0');
        $this->assertSame('1.0', $this->facet->getStart());
        $this->facet->setStart('NOW');
        $this->assertSame('NOW', $this->facet->getStart());
    }

    public function testSetAndGetEnd()
    {
        $this->facet->setEnd(100);
        $this->assertSame('100', $this->facet->getEnd());
        $this->facet->setEnd('100.0');
        $this->assertSame('100.0', $this->facet->getEnd());
        $this->facet->setEnd('NOW');
        $this->assertSame('NOW', $this->facet->getEnd());
    }

    public function testSetAndGetGap()
    {
        $this->facet->setGap(10);
        $this->assertSame('10', $this->facet->getGap());
    }

    public function testSetAndGetHardend()
    {
        $this->facet->setHardend(true);
        $this->assertTrue($this->facet->getHardend());
    }

    public function testSetAndGetOther()
    {
        $this->facet->setOther('all');
        $this->assertSame(['all'], $this->facet->getOther());
    }

    public function testSetAndGetOtherArray()
    {
        $this->facet->setOther(['before', 'after']);
        $this->assertSame(['before', 'after'], $this->facet->getOther());
    }

    public function testSetAndGetInclude()
    {
        $this->facet->setInclude('all');
        $this->assertSame(['all'], $this->facet->getInclude());
    }

    public function testSetAndGetIncludeArray()
    {
        $this->facet->setInclude(['lower', 'upper']);
        $this->assertSame(['lower', 'upper'], $this->facet->getInclude());
    }
}
