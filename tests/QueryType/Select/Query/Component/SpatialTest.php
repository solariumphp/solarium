<?php

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\Spatial;
use Solarium\QueryType\Select\Query\Query;

class SpatialTest extends \PHPUnit_Framework_TestCase
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

        $this->assertEquals($options['sfield'], $this->spatial->getField());
        $this->assertEquals($options['d'], $this->spatial->getDistance());
        $this->assertEquals($options['pt'], $this->spatial->getPoint());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMPONENT_SPATIAL,
            $this->spatial->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertEquals(null, $this->spatial->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\Spatial',
            $this->spatial->getRequestBuilder()
        );
    }

    public function testSetAndGetField()
    {
        $value = 'geo';
        $this->spatial->setField($value);

        $this->assertEquals(
            $value,
            $this->spatial->getField()
        );
    }

    public function testSetAndGetDistance()
    {
        $value = 'distance';
        $this->spatial->setDistance($value);

        $this->assertEquals(
            $value,
            $this->spatial->getDistance()
        );
    }

    public function testSetAndGetPoint()
    {
        $value = '52,13';
        $this->spatial->setPoint($value);

        $this->assertEquals(
            $value,
            $this->spatial->getPoint()
        );
    }
}
