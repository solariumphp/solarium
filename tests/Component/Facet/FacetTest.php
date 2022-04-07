<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\AbstractFacet;
use Solarium\Component\Facet\FacetInterface;

class FacetTest extends TestCase
{
    /**
     * @var FacetInterface
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new TestFacet();
    }

    public function testConfigMode()
    {
        $this->facet->setOptions(['local_key' => 'myKey', 'local_exclude' => ['e1', 'e2']]);
        $this->assertSame('myKey', $this->facet->getKey());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testConfigModeWithSingleValueExclude()
    {
        $this->facet->setOptions(['local_exclude' => 'e1']);
        $this->assertEquals(['e1'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetAndGetKey()
    {
        $this->facet->setKey('testkey');
        $this->assertSame('testkey', $this->facet->getKey());
    }

    public function testAddExclude()
    {
        $this->facet->getLocalParameters()->setExclude('e1');
        $this->assertEquals(['e1'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testAddExcludes()
    {
        $this->facet->getLocalParameters()->addExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testRemoveExclude()
    {
        $this->facet->getLocalParameters()->addExcludes(['e1', 'e2']);
        $this->facet->getLocalParameters()->removeExclude('e1');
        $this->assertEquals(['e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testClearExcludes()
    {
        $this->facet->getLocalParameters()->addExcludes(['e1', 'e2']);
        $this->facet->getLocalParameters()->clearExcludes();
        $this->assertEquals([], $this->facet->getLocalParameters()->getExcludes());
    }
}

class TestFacet extends AbstractFacet
{
    public function getType(): string
    {
        return 'test';
    }
}
