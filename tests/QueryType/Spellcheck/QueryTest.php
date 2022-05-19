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

    public function setUp(): void
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
            [$value],
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
        $this->query->setOnlyMorePopular(false);
        $this->assertFalse($this->query->getOnlyMorePopular());
    }

    public function testSetAndGetAlternativeTermCount()
    {
        $value = 5;
        $this->query->setAlternativeTermCount($value);

        $this->assertEquals(
            $value,
            $this->query->getAlternativeTermCount()
        );
    }

    public function testSetAndGetExtendedResults()
    {
        $this->query->setExtendedResults(false);
        $this->assertFalse($this->query->getExtendedResults());
    }

    public function testSetAndGetCollate()
    {
        $this->query->setCollate(false);
        $this->assertFalse($this->query->getCollate());
    }
}
