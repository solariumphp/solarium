<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field;
use Solarium\Component\FacetSet;

class FieldTest extends TestCase
{
    protected Field $facet;

    public function setUp(): void
    {
        $this->facet = new Field();
    }

    public function testConfigMode(): void
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'local_terms' => ['t1', 't2'],
            'field' => 'text',
            'prefix' => 'xyz',
            'contains' => 'foobar',
            'containsignorecase' => true,
            'matches' => '^foo.*',
            'sort' => 'index',
            'limit' => 10,
            'offset' => 20,
            'mincount' => 5,
            'missing' => true,
            'method' => 'enum',
            'enum.cache.minDf' => 15,
            'exists' => true,
            'excludeTerms' => 'foo,bar',
            'overrequest.count' => 20,
            'overrequest.ratio' => 2.5,
            'threads' => 42,
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getExcludes());
        $this->assertSame($options['local_terms'], $this->facet->getTerms());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame($options['prefix'], $this->facet->getPrefix());
        $this->assertSame($options['contains'], $this->facet->getContains());
        $this->assertTrue($this->facet->getContainsIgnoreCase());
        $this->assertSame($options['matches'], $this->facet->getMatches());
        $this->assertSame($options['sort'], $this->facet->getSort());
        $this->assertSame($options['limit'], $this->facet->getLimit());
        $this->assertSame($options['offset'], $this->facet->getOffset());
        $this->assertSame($options['mincount'], $this->facet->getMinCount());
        $this->assertTrue($this->facet->getMissing());
        $this->assertSame($options['method'], $this->facet->getMethod());
        $this->assertSame($options['enum.cache.minDf'], $this->facet->getEnumCacheMinimumDocumentFrequency());
        $this->assertTrue($this->facet->getExists());
        $this->assertSame($options['excludeTerms'], $this->facet->getExcludeTerms());
        $this->assertSame($options['overrequest.count'], $this->facet->getOverrequestCount());
        $this->assertSame($options['overrequest.ratio'], $this->facet->getOverrequestRatio());
        $this->assertSame($options['threads'], $this->facet->getThreads());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            FacetSet::FACET_FIELD,
            $this->facet->getType()
        );
    }

    public function testSetAndGetField(): void
    {
        $this->facet->setField('category');
        $this->assertSame('category', $this->facet->getField());
    }

    public function testAddTerm(): void
    {
        $this->facet->addTerm('t1');
        $this->assertEquals(['t1'], $this->facet->getTerms());
        $this->assertEquals(['t1'], $this->facet->getLocalParameters()->getTerms());

        $this->facet->addTerm('t2');
        $this->assertEquals(['t1', 't2'], $this->facet->getTerms());
        $this->assertEquals(['t1', 't2'], $this->facet->getLocalParameters()->getTerms());
    }

    public function testAddTerms(): void
    {
        $this->facet->addTerms(['t1', 't2']);
        $this->assertEquals(['t1', 't2'], $this->facet->getTerms());
        $this->assertEquals(['t1', 't2'], $this->facet->getLocalParameters()->getTerms());

        $this->facet->addTerms('t3,t4');
        $this->assertEquals(['t1', 't2', 't3', 't4'], $this->facet->getTerms());
        $this->assertEquals(['t1', 't2', 't3', 't4'], $this->facet->getLocalParameters()->getTerms());
    }

    public function testSetTerms(): void
    {
        $this->facet->setTerms(['t1', 't2']);
        $this->assertEquals(['t1', 't2'], $this->facet->getTerms());
        $this->assertEquals(['t1', 't2'], $this->facet->getLocalParameters()->getTerms());

        $this->facet->setTerms('t3,t4');
        $this->assertEquals(['t3', 't4'], $this->facet->getTerms());
        $this->assertEquals(['t3', 't4'], $this->facet->getLocalParameters()->getTerms());
    }

    public function testSetAndAddTermsWithEscapedSeparator(): void
    {
        $this->facet->setTerms('t1\,t2,t3');
        $this->assertEquals(['t1\,t2', 't3'], $this->facet->getTerms());
        $this->assertEquals(['t1\,t2', 't3'], $this->facet->getLocalParameters()->getTerms());

        $this->facet->addTerms('t4\,t5,t6');
        $this->assertEquals(['t1\,t2', 't3', 't4\,t5', 't6'], $this->facet->getTerms());
        $this->assertEquals(['t1\,t2', 't3', 't4\,t5', 't6'], $this->facet->getLocalParameters()->getTerms());
    }

    public function testRemoveTerm(): void
    {
        $this->facet->setTerms(['t1', 't2']);
        $this->facet->removeTerm('t1');
        $this->assertEquals(['t2'], $this->facet->getTerms());
        $this->assertEquals(['t2'], $this->facet->getLocalParameters()->getTerms());
    }

    public function testClearTerms(): void
    {
        $this->facet->setTerms(['t1', 't2']);
        $this->facet->clearTerms();
        $this->assertEquals([], $this->facet->getTerms());
        $this->assertEquals([], $this->facet->getLocalParameters()->getTerms());
    }

    public function testSetAndGetPrefix(): void
    {
        $this->facet->setPrefix('xyz');
        $this->assertSame('xyz', $this->facet->getPrefix());
    }

    public function testSetAndGetContains(): void
    {
        $this->facet->setContains('foobar');
        $this->assertSame('foobar', $this->facet->getContains());
    }

    public function testSetAndGetContainsIgnoreCase(): void
    {
        $this->facet->setContainsIgnoreCase(true);
        $this->assertTrue($this->facet->getContainsIgnoreCase());
    }

    public function testSetAndGetMatches(): void
    {
        $this->facet->setMatches('^foo.*');
        $this->assertSame('^foo.*', $this->facet->getMatches());
    }

    public function testSetAndGetSort(): void
    {
        $this->facet->setSort('index');
        $this->assertSame('index', $this->facet->getSort());
    }

    public function testSetAndGetLimit(): void
    {
        $this->facet->setLimit(12);
        $this->assertSame(12, $this->facet->getLimit());
    }

    public function testSetAndGetOffset(): void
    {
        $this->facet->setOffset(40);
        $this->assertSame(40, $this->facet->getOffset());
    }

    public function testSetAndGetMinCount(): void
    {
        $this->facet->setMinCount(100);
        $this->assertSame(100, $this->facet->getMinCount());
    }

    public function testSetAndGetMissing(): void
    {
        $this->facet->setMissing(true);
        $this->assertTrue($this->facet->getMissing());
    }

    public function testSetAndGetMethod(): void
    {
        $this->facet->setMethod('enum');
        $this->assertSame('enum', $this->facet->getMethod());
    }

    public function testSetAndGetEnumCacheMinimmumDocumentFrequency(): void
    {
        $this->facet->setEnumCacheMinimumDocumentFrequency(15);
        $this->assertSame(15, $this->facet->getEnumCacheMinimumDocumentFrequency());
    }

    public function testSetAndGetExists(): void
    {
        $this->facet->setExists(true);
        $this->assertTrue($this->facet->getExists());
    }

    public function testSetAndGetExcludeTerms(): void
    {
        $this->facet->setExcludeTerms('foo,bar');
        $this->assertSame('foo,bar', $this->facet->getExcludeTerms());
    }

    public function testSetAndGetOverrequestCount(): void
    {
        $this->facet->setOverrequestCount(20);
        $this->assertSame(20, $this->facet->getOverrequestCount());
    }

    public function testSetAndGetOverrequestRatio(): void
    {
        $this->facet->setOverrequestRatio(2.5);
        $this->assertSame(2.5, $this->facet->getOverrequestRatio());
    }

    public function testSetAndGetThreads(): void
    {
        $this->facet->setThreads(42);
        $this->assertSame(42, $this->facet->getThreads());
    }
}
