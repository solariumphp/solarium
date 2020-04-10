<?php

namespace Solarium\Tests\QueryType\Suggester;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Suggester\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_SUGGESTER, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Suggester\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Suggester\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->query->setQuery($value);

        $this->assertSame(
            $value,
            $this->query->getQuery()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->query->setDictionary($value);

        $this->assertSame(
            [$value],
            $this->query->getDictionary()
        );
    }

    public function testSetAndGetDictionaries()
    {
        $value = ['myDictionary1', 'myDictionary1'];
        $this->query->setDictionary($value);

        $this->assertSame(
            $value,
            $this->query->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->query->setCount($value);

        $this->assertSame(
            $value,
            $this->query->getCount()
        );
    }

    public function testSetAndGetContextFilterQuery()
    {
        $value = 'context filter query';
        $this->query->setContextFilterQuery($value);

        $this->assertSame(
            $value,
            $this->query->getContextFilterQuery()
        );
    }

    public function testSetAndBuild()
    {
        $this->assertFalse($this->query->getBuild());
        $this->query->setBuild(true);
        $this->assertTrue($this->query->getBuild());
    }

    public function testSetAndReload()
    {
        $this->assertFalse($this->query->getReload());
        $this->query->setReload(true);
        $this->assertTrue($this->query->getReload());
    }
}
