<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Range;
use Solarium\Component\FacetSet;

class RangeTest extends TestCase
{
    /**
     * @var Range
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Range();
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1', 'e2'),
            'field' => 'content',
            'start' => 1,
            'end' => 100,
            'gap' => 10,
            'hardend' => true,
            'other' => 'all',
            'include' => 'lower',
        );

        $this->facet->setOptions($options);

        $this->assertSame($options['key'], $this->facet->getKey());
        $this->assertSame($options['exclude'], $this->facet->getExcludes());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame($options['start'], $this->facet->getStart());
        $this->assertSame($options['end'], $this->facet->getEnd());
        $this->assertSame($options['gap'], $this->facet->getGap());
        $this->assertSame($options['hardend'], $this->facet->getHardend());
        $this->assertSame(array($options['other']), $this->facet->getOther());
        $this->assertSame(array($options['include']), $this->facet->getInclude());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::FACET_RANGE,
            $this->facet->getType()
        );
    }

    public function testSetMinCount()
    {
        $this->facet->setMinCount(5);

        $this->assertSame(5, $this->facet->getMinCount());
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('price');
        $this->assertSame('price', $this->facet->getField());
    }

    public function testSetAndGetStart()
    {
        $this->facet->setStart(1);
        $this->assertSame(1, $this->facet->getStart());
    }

    public function testSetAndGetEnd()
    {
        $this->facet->setEnd(100);
        $this->assertSame(100, $this->facet->getEnd());
    }

    public function testSetAndGetGap()
    {
        $this->facet->setGap(10);
        $this->assertSame(10, $this->facet->getGap());
    }

    public function testSetAndGetHardend()
    {
        $this->facet->setHardend(true);
        $this->assertTrue($this->facet->getHardend());
    }

    public function testSetAndGetOther()
    {
        $this->facet->setOther('all');
        $this->assertSame(array('all'), $this->facet->getOther());
    }

    public function testSetAndGetOtherArray()
    {
        $this->facet->setOther(array('before', 'after'));
        $this->assertSame(array('before', 'after'), $this->facet->getOther());
    }

    public function testSetAndGetInclude()
    {
        $this->facet->setInclude('all');
        $this->assertSame(array('all'), $this->facet->getInclude());
    }

    public function testSetAndGetIncludeArray()
    {
        $this->facet->setInclude(array('lower', 'upper'));
        $this->assertSame(array('lower', 'upper'), $this->facet->getInclude());
    }
}
