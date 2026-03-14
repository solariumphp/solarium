<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\AbstractFacet;

class FacetTest extends TestCase
{
    protected TestFacet $facet;

    public function setUp(): void
    {
        $this->facet = new TestFacet();
    }

    public function testConfigMode(): void
    {
        $this->facet->setOptions(['local_key' => 'myKey', 'local_exclude' => ['e1', 'e2']]);
        $this->assertSame('myKey', $this->facet->getKey());
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testConfigModeWithSingleValueExclude(): void
    {
        $this->facet->setOptions(['local_exclude' => 'e1']);
        $this->assertEquals(['e1'], $this->facet->getExcludes());
        $this->assertEquals(['e1'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetAndGetKey(): void
    {
        $this->facet->setKey('testkey');
        $this->assertSame('testkey', $this->facet->getKey());
    }

    public function testAddExclude(): void
    {
        $this->facet->addExclude('e1');
        $this->assertEquals(['e1'], $this->facet->getExcludes());
        $this->assertEquals(['e1'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExclude('e2');
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testAddExcludes(): void
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExcludes('e3,e4');
        $this->assertEquals(['e1', 'e2', 'e3', 'e4'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2', 'e3', 'e4'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetExcludes(): void
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->setExcludes('e3,e4');
        $this->assertEquals(['e3', 'e4'], $this->facet->getExcludes());
        $this->assertEquals(['e3', 'e4'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetAndAddTermsWithEscapedSeparator(): void
    {
        $this->facet->setExcludes('e1\,e2,e3');
        $this->assertEquals(['e1\,e2', 'e3'], $this->facet->getExcludes());
        $this->assertEquals(['e1\,e2', 'e3'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExcludes('e4\,e5,e6');
        $this->assertEquals(['e1\,e2', 'e3', 'e4\,e5', 'e6'], $this->facet->getExcludes());
        $this->assertEquals(['e1\,e2', 'e3', 'e4\,e5', 'e6'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testRemoveExclude(): void
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->facet->removeExclude('e1');
        $this->assertEquals(['e2'], $this->facet->getExcludes());
        $this->assertEquals(['e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testClearExcludes(): void
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->facet->clearExcludes();
        $this->assertEquals([], $this->facet->getExcludes());
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
