<?php

namespace Solarium\Tests\QueryType\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Spellcheck\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_SPELLCHECK, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Spellcheck\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Spellcheck\RequestBuilder', $this->query->getRequestBuilder());
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

    public function testSetAndGetOnlyMorePopular()
    {
        $value = false;
        $this->query->setOnlyMorePopular($value);

        $this->assertSame(
            $value,
            $this->query->getOnlyMorePopular()
        );
    }

    public function testSetAndGetCollate()
    {
        $value = false;
        $this->query->setCollate($value);

        $this->assertSame(
            $value,
            $this->query->getCollate()
        );
    }
}
