<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\AbstractFacet;

class FacetTest extends TestCase
{
    /**
     * @var AbstractFacet
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new TestFacet;
    }

    public function testConfigMode()
    {
        $this->facet->setOptions(array('key' => 'myKey', 'exclude' => array('e1', 'e2')));
        $this->assertSame('myKey', $this->facet->getKey());
        $this->assertEquals(array('e1', 'e2'), $this->facet->getExcludes());
    }

    public function testConfigModeWithSingleValueExclude()
    {
        $this->facet->setOptions(array('exclude' => 'e1'));
        $this->assertEquals(array('e1'), $this->facet->getExcludes());
    }

    public function testSetAndGetKey()
    {
        $this->facet->setKey('testkey');
        $this->assertSame('testkey', $this->facet->getKey());
    }

    public function testAddExclude()
    {
        $this->facet->addExclude('e1');
        $this->assertEquals(array('e1'), $this->facet->getExcludes());
    }

    public function testAddExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->assertEquals(array('e1', 'e2'), $this->facet->getExcludes());
    }

    public function testRemoveExclude()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->removeExclude('e1');
        $this->assertEquals(array('e2'), $this->facet->getExcludes());
    }

    public function testClearExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->clearExcludes();
        $this->assertEquals(array(), $this->facet->getExcludes());
    }

    public function testSetExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->setExcludes(array('e3', 'e4'));
        $this->assertEquals(array('e3', 'e4'), $this->facet->getExcludes());
    }
}

class TestFacet extends AbstractFacet
{
    public function getType()
    {
        return 'test';
    }
}
