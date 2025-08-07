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

    public function setUp(): void
    {
        $this->spatial = new Spatial();
    }

    public function testConfigMode(): void
    {
        $options = [
            'sfield' => 'geo',
            'd' => 50.1415,
            'pt' => '48.2233,16.3161',
        ];

        $this->spatial->setOptions($options);

        $this->assertEquals($options['sfield'], $this->spatial->getField());
        $this->assertEquals($options['d'], $this->spatial->getDistance());
        $this->assertEquals($options['pt'], $this->spatial->getPoint());
    }

    public function testGetType(): void
    {
        $this->assertEquals(
            Query::COMPONENT_SPATIAL,
            $this->spatial->getType()
        );
    }

    public function testGetResponseParser(): void
    {
        $this->assertNull($this->spatial->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Spatial',
            $this->spatial->getRequestBuilder()
        );
    }

    public function testSetAndGetField(): void
    {
        $value = 'geo';
        $this->spatial->setField($value);

        $this->assertEquals($value, $this->spatial->getField());
    }

    public function testSetAndGetDistance(): void
    {
        $value = 5.9438;
        $this->spatial->setDistance($value);

        $this->assertEquals($value, $this->spatial->getDistance());
    }

    public function testSetAndGetPoint(): void
    {
        $value = '52,13';
        $this->spatial->setPoint($value);

        $this->assertEquals($value, $this->spatial->getPoint());
    }
}
