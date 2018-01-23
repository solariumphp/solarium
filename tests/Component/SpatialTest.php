<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Spatial;
use Solarium\QueryType\Select\Query\Query;

class SpatialTest extends TestCase
{
    /**
     * @var Spatial
     */
    protected $spatial;

    public function setUp()
    {
        $this->spatial = new Spatial;
    }

    public function testConfigMode()
    {
        $options = array(
            'sfield' => 'geo',
            'd' => 50,
            'pt' => '48.2233,16.3161',
        );

        $this->spatial->setOptions($options);

        $this->assertSame($options['sfield'], $this->spatial->getField());
        $this->assertSame($options['d'], $this->spatial->getDistance());
        $this->assertSame($options['pt'], $this->spatial->getPoint());
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMPONENT_SPATIAL,
            $this->spatial->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertSame(null, $this->spatial->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Spatial',
            $this->spatial->getRequestBuilder()
        );
    }

    public function testSetAndGetField()
    {
        $value = 'geo';
        $this->spatial->setField($value);

        $this->assertSame(
            $value,
            $this->spatial->getField()
        );
    }

    public function testSetAndGetDistance()
    {
        $value = 'distance';
        $this->spatial->setDistance($value);

        $this->assertSame(
            $value,
            $this->spatial->getDistance()
        );
    }

    public function testSetAndGetPoint()
    {
        $value = '52,13';
        $this->spatial->setPoint($value);

        $this->assertSame(
            $value,
            $this->spatial->getPoint()
        );
    }
}
