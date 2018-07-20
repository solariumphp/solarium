<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ReRankQuery;
use Solarium\QueryType\Select\Query\Query;

class ReRankQueryTest extends TestCase
{
    /**
     * @var ReRankQuery
     */
    protected $reRankQuery;

    public function setUp()
    {
        $this->reRankQuery = new ReRankQuery();
    }

    public function testConfigMode()
    {
        $options = [
            'query' => 'foo:bar',
            'docs' => 50,
            'weight' => '16.3161',
        ];

        $this->reRankQuery->setOptions($options);

        $this->assertEquals($options['query'], $this->reRankQuery->getQuery());
        $this->assertEquals($options['docs'], $this->reRankQuery->getDocs());
        $this->assertEquals($options['weight'], $this->reRankQuery->getWeight());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMPONENT_RERANKQUERY,
            $this->reRankQuery->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertNull($this->reRankQuery->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\ReRankQuery',
            $this->reRankQuery->getRequestBuilder()
        );
    }

    public function testSetAndGetQuery()
    {
        $this->reRankQuery->setQuery('category:1');
        $this->assertSame('category:1', $this->reRankQuery->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->reRankQuery->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->reRankQuery->getQuery());
    }

    public function testSetAndGetDocs()
    {
        $value = 42;
        $this->reRankQuery->setDocs($value);

        $this->assertEquals($value, $this->reRankQuery->getDocs());
    }

    public function testSetAndGetWeight()
    {
        $value = '52.13';
        $this->reRankQuery->setWeight($value);

        $this->assertEquals($value, $this->reRankQuery->getWeight());
    }
}
