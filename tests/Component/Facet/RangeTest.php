<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Range;
use Solarium\Component\FacetSet;

class RangeTest extends TestCase
{
    /**
     * @var Range
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new Range();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'field' => 'content',
            'start' => 1,
            'end' => 100,
            'gap' => 10,
            'hardend' => true,
            'other' => 'all',
            'include' => 'lower',
            'tag' => 'myTag',
            'pivot' => ['pivot', 'fields'],
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getExcludes());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame((string) $options['start'], $this->facet->getStart());
        $this->assertSame((string) $options['end'], $this->facet->getEnd());
        $this->assertSame((string) $options['gap'], $this->facet->getGap());
        $this->assertTrue($this->facet->getHardend());
        $this->assertSame([$options['other']], $this->facet->getOther());
        $this->assertSame([$options['include']], $this->facet->getInclude());
    }

    public function testConfigModeWithExclude()
    {
        $options = [
            'exclude' => 'e1\,e2,e3',
        ];

        @$this->facet->setOptions($options);

        $this->assertSame(['e1\,e2', 'e3'], $this->facet->getExcludes());
    }

    public function testConfigModeWithExcludeThrowsDeprecation()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_USER_DEPRECATED);

        $options = [
            'exclude' => 'e1\,e2,e3',
        ];

        $this->expectExceptionCode(\E_USER_DEPRECATED);
        $this->facet->setOptions($options);

        restore_error_handler();
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
        $this->assertSame('1', $this->facet->getStart());
    }

    public function testSetAndGetEnd()
    {
        $this->facet->setEnd(100);
        $this->assertSame('100', $this->facet->getEnd());
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

    public function testSetAndGetPivot()
    {
        $this->facet->setPivot(['pivot', 'fields']);
        $this->assertSame(['pivot', 'fields'], $this->facet->getPivot());
    }
}
