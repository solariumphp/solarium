<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryElevation;

class QueryElevationTest extends TestCase
{
    /**
     * @var QueryElevation
     */
    protected $queryelevation;

    public function setUp(): void
    {
        $this->queryelevation = new QueryElevation();
    }

    public function testConfigMode()
    {
        $options = [
            'transformers' => '[transformer]',
            'enableElevation' => false,
            'forceElevation' => true,
            'exclusive' => true,
            'useConfiguredElevatedOrder' => false,
            'markExcludes' => false,
            'elevateIds' => 'doc1,doc2',
            'excludeIds' => 'doc3,doc4',
            'excludeTags' => 'tagA,tagB',
        ];

        $this->queryelevation->setOptions($options);

        $this->assertSame(['[transformer]'], $this->queryelevation->getTransformers());
        $this->assertFalse($this->queryelevation->getEnableElevation());
        $this->assertTrue($this->queryelevation->getForceElevation());
        $this->assertTrue($this->queryelevation->getExclusive());
        $this->assertFalse($this->queryelevation->getUseConfiguredElevatedOrder());
        $this->assertFalse($this->queryelevation->getMarkExcludes());
        $this->assertSame(['doc1', 'doc2'], $this->queryelevation->getElevateIds());
        $this->assertSame(['doc3', 'doc4'], $this->queryelevation->getExcludeIds());
        $this->assertSame(['tagA', 'tagB'], $this->queryelevation->getExcludeTags());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION, $this->queryelevation->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertNull($this->queryelevation->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\QueryElevation',
            $this->queryelevation->getRequestBuilder()
        );
    }

    public function testAddTransformer()
    {
        $expectedTrans = $this->queryelevation->getTransformers();
        $expectedTrans[] = '[newtrans]';
        $this->queryelevation->addTransformer('[newtrans]');
        $this->assertSame($expectedTrans, $this->queryelevation->getTransformers());
    }

    public function testClearTransformers()
    {
        $this->queryelevation->addTransformer('[newtrans]');
        $this->queryelevation->clearTransformers();
        $this->assertSame([], $this->queryelevation->getTransformers());
    }

    public function testAddTransformers()
    {
        $transformers = ['[trans1]', '[trans2]'];

        $this->queryelevation->clearTransformers();
        $this->queryelevation->addTransformers($transformers);
        $this->assertSame($transformers, $this->queryelevation->getTransformers());
    }

    public function testAddTransformersAsStringWithTrim()
    {
        $this->queryelevation->clearTransformers();
        $this->queryelevation->addTransformers('[trans1], [trans2]');
        $this->assertSame(['[trans1]', '[trans2]'], $this->queryelevation->getTransformers());
    }

    public function testRemoveTransformer()
    {
        $this->queryelevation->clearTransformers();
        $this->queryelevation->addTransformers(['[trans1]', '[trans2]']);
        $this->queryelevation->removeTransformer('[trans1]');
        $this->assertSame(['[trans2]'], $this->queryelevation->getTransformers());
    }

    public function testSetTransformers()
    {
        $this->queryelevation->clearTransformers();
        $this->queryelevation->addTransformers(['[trans1]', '[trans2]']);
        $this->queryelevation->setTransformers(['[trans3]', '[trans4]']);
        $this->assertSame(['[trans3]', '[trans4]'], $this->queryelevation->getTransformers());
    }

    public function testSetAndGetEnableElevation()
    {
        $this->queryelevation->setEnableElevation(false);
        $this->assertFalse($this->queryelevation->getEnableElevation());
    }

    public function testSetAndGetForceElevation()
    {
        $this->queryelevation->setForceElevation(true);
        $this->assertTrue($this->queryelevation->getForceElevation());
    }

    public function testSetAndGetExclusive()
    {
        $this->queryelevation->setExclusive(true);
        $this->assertTrue($this->queryelevation->getExclusive());
    }

    public function testSetAndGetUseConfiguredElevatedOrder()
    {
        $this->queryelevation->setUseConfiguredElevatedOrder(false);
        $this->assertFalse($this->queryelevation->getUseConfiguredElevatedOrder());
    }

    public function testSetMarkExcludesTrue()
    {
        $this->queryelevation->removeTransformer('[excluded]');
        $this->queryelevation->setMarkExcludes(true);
        $this->assertTrue($this->queryelevation->getMarkExcludes());
        $this->assertContains('[excluded]', $this->queryelevation->getTransformers());
    }

    public function testSetMarkExcludesFalse()
    {
        $this->queryelevation->addTransformer('[excluded]');
        $this->queryelevation->setMarkExcludes(false);
        $this->assertFalse($this->queryelevation->getMarkExcludes());
        $this->assertNotContains('[excluded]', $this->queryelevation->getTransformers());
    }

    public function testSetAndGetElevateIds()
    {
        $ids = ['doc1', 'doc2'];

        $this->queryelevation->setElevateIds($ids);
        $this->assertSame($ids, $this->queryelevation->getElevateIds());
    }

    public function testSetElevateIdsAsStringWithTrim()
    {
        $this->queryelevation->setElevateIds('doc1, doc2');
        $this->assertSame(['doc1', 'doc2'], $this->queryelevation->getElevateIds());
    }

    public function testSetAndGetExcludeIds()
    {
        $ids = ['doc3', 'doc4'];

        $this->queryelevation->setExcludeIds($ids);
        $this->assertSame($ids, $this->queryelevation->getExcludeIds());
    }

    public function testSetExcludeIdsAsStringWithTrim()
    {
        $this->queryelevation->setExcludeIds('doc3, doc4');
        $this->assertSame(['doc3', 'doc4'], $this->queryelevation->getExcludeIds());
    }

    public function testSetAndGetExcludeTags()
    {
        $tags = ['tagA', 'tagB'];

        $this->queryelevation->setExcludeTags($tags);
        $this->assertSame($tags, $this->queryelevation->getExcludeTags());
    }

    public function testSetExcludeTagsAsStringWithTrim()
    {
        $this->queryelevation->setExcludeTags('tagA, tagB');
        $this->assertSame(['tagA', 'tagB'], $this->queryelevation->getExcludeTags());
    }
}
