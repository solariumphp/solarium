<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\AbstractFacet;
use Solarium\Component\Facet\ExcludeTagsTrait;
use Solarium\Component\Facet\FacetInterface;

class FacetTest extends TestCase
{
    /**
     * @var FacetInterface
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new TestFacet();
    }

    public function testConfigMode()
    {
        $this->facet->setOptions(['key' => 'myKey', 'exclude' => ['e1', 'e2']]);
        $this->assertSame('myKey', $this->facet->getKey());
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
    }

    public function testConfigModeWithSingleValueExclude()
    {
        $this->facet->setOptions(['exclude' => 'e1']);
        $this->assertEquals(['e1'], $this->facet->getExcludes());
    }

    public function testSetAndGetKey()
    {
        $this->facet->setKey('testkey');
        $this->assertSame('testkey', $this->facet->getKey());
    }

    public function testAddExclude()
    {
        $this->facet->addExclude('e1');
        $this->assertEquals(['e1'], $this->facet->getExcludes());
    }

    public function testAddExcludes()
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
    }

    public function testRemoveExclude()
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->facet->removeExclude('e1');
        $this->assertEquals(['e2'], $this->facet->getExcludes());
    }

    public function testClearExcludes()
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->facet->clearExcludes();
        $this->assertEquals([], $this->facet->getExcludes());
    }

    public function testSetExcludes()
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->facet->setExcludes(['e3', 'e4']);
        $this->assertEquals(['e3', 'e4'], $this->facet->getExcludes());
    }
}

class TestFacet extends AbstractFacet
{
    use ExcludeTagsTrait;

    public function getType(): string
    {
        return 'test';
    }
}
