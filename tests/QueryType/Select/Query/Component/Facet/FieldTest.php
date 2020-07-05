<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field;
use Solarium\Component\FacetSet;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new Field();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'field' => 'text',
            'sort' => 'index',
            'limit' => 10,
            'offset' => 20,
            'mincount' => 5,
            'missing' => true,
            'method' => 'enum',
            'contains' => 'foobar',
            'containsignorecase' => true,
            'excludeTerms' => 'foo,bar',
            'matches' => '^foo.*',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getLocalParameters()->getExcludes());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame($options['sort'], $this->facet->getSort());
        $this->assertSame($options['limit'], $this->facet->getLimit());
        $this->assertSame($options['offset'], $this->facet->getOffset());
        $this->assertSame($options['mincount'], $this->facet->getMinCount());
        $this->assertTrue($this->facet->getMissing());
        $this->assertSame($options['method'], $this->facet->getMethod());
        $this->assertSame($options['contains'], $this->facet->getContains());
        $this->assertTrue($this->facet->getContainsIgnoreCase());
        $this->assertSame($options['excludeTerms'], $this->facet->getExcludeTerms());
        $this->assertSame($options['matches'], $this->facet->getMatches());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::FACET_FIELD,
            $this->facet->getType()
        );
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('category');
        $this->assertSame('category', $this->facet->getField());
    }

    public function testSetAndGetSort()
    {
        $this->facet->setSort('index');
        $this->assertSame('index', $this->facet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->facet->setPrefix('xyz');
        $this->assertSame('xyz', $this->facet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->facet->setLimit(12);
        $this->assertSame(12, $this->facet->getLimit());
    }

    public function testSetAndGetOffset()
    {
        $this->facet->setOffset(40);
        $this->assertSame(40, $this->facet->getOffset());
    }

    public function testSetAndGetMinCount()
    {
        $this->facet->setMinCount(100);
        $this->assertSame(100, $this->facet->getMinCount());
    }

    public function testSetAndGetMissing()
    {
        $this->facet->setMissing(true);
        $this->assertTrue($this->facet->getMissing());
    }

    public function testSetAndGetMethod()
    {
        $this->facet->setMethod('enum');
        $this->assertSame('enum', $this->facet->getMethod());
    }

    public function testSetAndGetContains()
    {
        $this->facet->setContains('foobar');
        $this->assertSame('foobar', $this->facet->getContains());
    }

    public function testSetAndGetContainsIgnoreCase()
    {
        $this->facet->setContainsIgnoreCase(true);
        $this->assertTrue($this->facet->getContainsIgnoreCase());
    }

    public function testSetAndGetExcludeTerms()
    {
        $this->facet->setExcludeTerms('foo,bar');
        $this->assertSame('foo,bar', $this->facet->getExcludeTerms());
    }

    public function testSetAndGetMatches()
    {
        $this->facet->setMatches('^foo.*');
        $this->assertSame('^foo.*', $this->facet->getMatches());
    }
}
