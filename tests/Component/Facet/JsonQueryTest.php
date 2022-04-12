<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\JsonQuery;
use Solarium\Component\FacetSet;

class JsonQueryTest extends TestCase
{
    /**
     * @var JsonQuery
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new JsonQuery();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'query' => 'category:1',
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['query'], $this->facet->getQuery());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::JSON_FACET_QUERY,
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
