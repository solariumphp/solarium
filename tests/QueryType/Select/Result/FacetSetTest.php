<?php

namespace Solarium\Tests\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\AbstractFacet;
use Solarium\Component\Result\Facet\FacetResultInterface;
use Solarium\Component\Result\FacetSet;

class FacetSetTest extends TestCase
{
    /**
     * @var FacetSet
     */
    protected $result;

    protected $facets;

    public function setUp(): void
    {
        $this->facets = [
            'facet1' => new DummyFacet(),
            'facet2' => new DummyFacet(),
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
        $this->assertCount(count($this->facets), $this->result);
    }
}

class DummyFacet extends AbstractFacet implements FacetResultInterface
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'dummy';
    }
}
