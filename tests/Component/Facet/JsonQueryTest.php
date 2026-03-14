<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\Facet\JsonQuery;
use Solarium\Component\FacetSet;

class JsonQueryTest extends TestCase
{
    protected JsonQuery $facet;

    public function setUp(): void
    {
        $this->facet = new JsonQuery();
    }

    public function testConfigMode(): void
    {
        $options = [
            'local_key' => 'myKey',
            'query' => 'category:1',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['query'], $this->facet->getQuery());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            FacetSet::JSON_FACET_QUERY,
            $this->facet->getType()
        );
    }

    public function testSetAndGetQuery(): void
    {
        $this->facet->setQuery('category:1');
        $this->assertSame('category:1', $this->facet->getQuery());
    }

    public function testSetAndGetQueryWithBind(): void
    {
        $this->facet->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->facet->getQuery());
    }

    public function testAddAndRemoveFacets(): void
    {
        $this->facet->addFacet(new JsonAggregation(['local_key' => 'f1', 'function' => 'avg(mul(price,popularity))']));
        $this->facet->addFacet(new JsonAggregation(['local_key' => 'f2', 'function' => 'unique(popularity)']));
        $this->assertSame(['f1', 'f2'], array_keys($this->facet->getFacets()));

        $this->facet->removeFacet('f1');
        $this->assertNull($this->facet->getFacet('f1'));
        $this->assertNotNull($this->facet->getFacet('f2'));
    }
}
