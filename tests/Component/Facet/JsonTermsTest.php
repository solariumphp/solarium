<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonTerms;
use Solarium\Component\FacetSet;

class JsonTermsTest extends TestCase
{
    protected JsonTerms $facet;

    public function setUp(): void
    {
        $this->facet = new JsonTerms();
    }

    public function testConfigMode(): void
    {
        $options = [
            'local_key' => 'myKey',
            'field' => 'text',
            'offset' => 20,
            'limit' => 10,
            'sort' => 'index asc',
            'overrequest' => 3,
            'refine' => true,
            'overrefine' => 15,
            'mincount' => 5,
            'missing' => true,
            'numBuckets' => true,
            'allBuckets' => true,
            'prefix' => 'xyz',
            'method' => 'enum',
            'prelim_sort' => 'count desc',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame($options['offset'], $this->facet->getOffset());
        $this->assertSame($options['limit'], $this->facet->getLimit());
        $this->assertSame($options['sort'], $this->facet->getSort());
        $this->assertSame($options['overrequest'], $this->facet->getOverRequest());
        $this->assertTrue($this->facet->getRefine());
        $this->assertSame($options['overrefine'], $this->facet->getOverRefine());
        $this->assertSame($options['mincount'], $this->facet->getMinCount());
        $this->assertTrue($this->facet->getMissing());
        $this->assertTrue($this->facet->getNumBuckets());
        $this->assertTrue($this->facet->getAllBuckets());
        $this->assertSame($options['prefix'], $this->facet->getPrefix());
        $this->assertSame($options['method'], $this->facet->getMethod());
        $this->assertSame($options['prelim_sort'], $this->facet->getPrelimSort());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            FacetSet::JSON_FACET_TERMS,
            $this->facet->getType()
        );
    }

    public function testSetAndGetField(): void
    {
        $this->facet->setField('category');
        $this->assertSame('category', $this->facet->getField());
    }

    public function testSetAndGetOffset(): void
    {
        $this->facet->setOffset(40);
        $this->assertSame(40, $this->facet->getOffset());
    }

    public function testSetAndGetLimit(): void
    {
        $this->facet->setLimit(12);
        $this->assertSame(12, $this->facet->getLimit());
    }

    public function testSetAndGetSort(): void
    {
        $this->facet->setSort('index asc');
        $this->assertSame('index asc', $this->facet->getSort());
    }

    public function testSetAndGetOverRequest(): void
    {
        $this->facet->setOverRequest(5);
        $this->assertSame(5, $this->facet->getOverRequest());
    }

    public function testSetAndGetRefine(): void
    {
        $this->facet->setRefine(true);
        $this->assertTrue($this->facet->getRefine());
    }

    public function testSetAndGetOverRefine(): void
    {
        $this->facet->setOverRefine(15);
        $this->assertSame(15, $this->facet->getOverRefine());
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

    public function testSetAndGetNumBuckets(): void
    {
        $this->facet->setNumBuckets(true);
        $this->assertTrue($this->facet->getNumBuckets());
    }

    public function testSetAndGetAllBuckets(): void
    {
        $this->facet->setAllBuckets(true);
        $this->assertTrue($this->facet->getAllBuckets());
    }

    public function testSetAndGetPrefix(): void
    {
        $this->facet->setPrefix('xyz');
        $this->assertSame('xyz', $this->facet->getPrefix());
    }

    public function testSetAndGetMethod(): void
    {
        $this->facet->setMethod('enum');
        $this->assertSame('enum', $this->facet->getMethod());
    }

    public function testSetAndGetPrelimSort(): void
    {
        $this->facet->setPrelimSort('count desc');
        $this->assertSame('count desc', $this->facet->getPrelimSort());
    }
}
