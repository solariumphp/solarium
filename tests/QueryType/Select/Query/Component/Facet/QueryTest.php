<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Query;
use Solarium\Component\FacetSet;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Query();
    }

    public function testConfigMode()
    {
        $options = [
            'key' => 'myKey',
            'exclude' => ['e1', 'e2'],
            'query' => 'category:1',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['key'], $this->facet->getKey());
        $this->assertSame($options['exclude'], $this->facet->getExcludes());
        $this->assertSame($options['query'], $this->facet->getQuery());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::FACET_QUERY,
            $this->facet->getType()
        );
    }

    public function testSetAndGetQuery()
    {
        $this->facet->setQuery('category:1');
        $this->assertSame('category:1', $this->facet->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->facet->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->facet->getQuery());
    }
}
