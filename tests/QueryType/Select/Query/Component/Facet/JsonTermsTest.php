<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonTerms;
use Solarium\Component\FacetSet;

class JsonTermsTest extends TestCase
{
    /**
     * @var JsonTerms
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new JsonTerms();
    }

    public function testConfigMode()
    {
        $options = [
            'key' => 'myKey',
            'field' => 'text',
            'sort' => 'index',
            'limit' => 10,
            'offset' => 20,
            'mincount' => 5,
            'missing' => true,
            'method' => 'enum',
            'refine' => true,
            'overrequest' => 3,
            'numBuckets' => true,
            'allBuckets' => true,
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['key'], $this->facet->getKey());
        $this->assertSame($options['field'], $this->facet->getField());
        $this->assertSame($options['sort'], $this->facet->getSort());
        $this->assertSame($options['limit'], $this->facet->getLimit());
        $this->assertSame($options['offset'], $this->facet->getOffset());
        $this->assertSame($options['mincount'], $this->facet->getMinCount());
        $this->assertSame($options['missing'], $this->facet->getMissing());
        $this->assertSame($options['method'], $this->facet->getMethod());
        $this->assertSame($options['refine'], $this->facet->getRefine());
        $this->assertSame($options['overrequest'], $this->facet->getOverRequest());
        $this->assertSame($options['numBuckets'], $this->facet->getNumBuckets());
        $this->assertSame($options['allBuckets'], $this->facet->getAllBuckets());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::JSON_FACET_TERMS,
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

    public function testSetAndGetRefine()
    {
        $this->facet->setRefine(true);
        $this->assertTrue($this->facet->getRefine());
    }

    public function testSetAndGetOverRequest()
    {
        $this->facet->setOverRequest(5);
        $this->assertSame(5, $this->facet->getOverRequest());
    }

    public function testSetAndGetNumBuckets()
    {
        $this->facet->setNumBuckets(true);
        $this->assertTrue($this->facet->getNumBuckets());
    }

    public function testSetAndGetAllBuckets()
    {
        $this->facet->setAllBuckets(true);
        $this->assertTrue($this->facet->getAllBuckets());
    }
}
