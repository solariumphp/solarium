<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Query;
use Solarium\Component\FacetSet;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new Query();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'query' => 'category:1',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getLocalParameters()->getExcludes());
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
