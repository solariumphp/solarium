<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ReRankQuery;
use Solarium\Exception\DomainException;
use Solarium\QueryType\Select\Query\Query;

class ReRankQueryTest extends TestCase
{
    /**
     * @var ReRankQuery
     */
    protected $reRankQuery;

    public function setUp(): void
    {
        $this->reRankQuery = new ReRankQuery();
    }

    public function testConfigMode()
    {
        $options = [
            'query' => 'foo:bar',
            'docs' => 50,
            'weight' => '16.3161',
            'scale' => '1-5',
            'mainscale' => '0-1',
            'operator' => ReRankQuery::OPERATOR_MULTIPLY,
        ];

        $this->reRankQuery->setOptions($options);

        $this->assertEquals($options['query'], $this->reRankQuery->getQuery());
        $this->assertEquals($options['docs'], $this->reRankQuery->getDocs());
        $this->assertEquals($options['weight'], $this->reRankQuery->getWeight());
        $this->assertEquals($options['scale'], $this->reRankQuery->getScale());
        $this->assertEquals($options['mainscale'], $this->reRankQuery->getMainScale());
        $this->assertEquals($options['operator'], $this->reRankQuery->getOperator());
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

    public function testSetAndGetScale()
    {
        $value = '0-1';
        $this->reRankQuery->setScale($value);

        $this->assertEquals($value, $this->reRankQuery->getScale());
    }

    public function testSetInvalidScale()
    {
        $this->expectException(DomainException::class);
        $this->reRankQuery->setScale('-1-0');
    }

    public function testSetAndGetMainScale()
    {
        $value = '1-10';
        $this->reRankQuery->setMainScale($value);

        $this->assertEquals($value, $this->reRankQuery->getMainScale());
    }

    public function testSetInvalidMainScale()
    {
        $this->expectException(DomainException::class);
        $this->reRankQuery->setMainScale('a-z');
    }

    public function testSetAndGetOperator()
    {
        $value = ReRankQuery::OPERATOR_REPLACE;
        $this->reRankQuery->setOperator($value);

        $this->assertEquals($value, $this->reRankQuery->getOperator());
    }

    /**
     * @testWith ["0-1"]
     *           ["1-10"]
     */
    public function testIsValidScale(string $value)
    {
        $this->assertTrue(ReRankQuery::isValidScale($value));
    }

    /**
     * @testWith ["0--1"]
     *           ["-1-0"]
     *           ["1"]
     *           ["a-z"]
     *           [""]
     */
    public function testIsInvalidScale(string $value)
    {
        $this->assertFalse(ReRankQuery::isValidScale($value));
    }
}
