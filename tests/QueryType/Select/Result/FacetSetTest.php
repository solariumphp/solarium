<?php

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\FacetSet;

class FacetSetTest extends TestCase
{
    /**
     * @var FacetSet
     */
    protected $result;

    protected $facets;

    public function setUp()
    {
        $this->facets = [
            'facet1' => 'content1',
            'facet2' => 'content2',
        ];

        $this->result = new FacetSet($this->facets);
    }

    public function testGetFacets()
    {
        $this->assertSame($this->facets, $this->result->getFacets());
    }

    public function testGetFacet()
    {
        $this->assertSame(
            $this->facets['facet2'],
            $this->result->getFacet('facet2')
        );
    }

    public function testGetInvalidFacet()
    {
        $this->assertNull(
            $this->result->getFacet('invalid')
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->facets, $items);
    }

    public function testCount()
    {
        $this->assertSame(count($this->facets), count($this->result));
    }
}
