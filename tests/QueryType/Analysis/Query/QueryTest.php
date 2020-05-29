<?php

namespace Solarium\Tests\QueryType\Analysis\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Analysis\Query\AbstractQuery;

class QueryTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new TestAnalysisQuery();
    }

    public function testSetAndGetQuery()
    {
        $querystring = 'test query values';

        $this->query->setQuery($querystring);
        $this->assertSame($querystring, $this->query->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->query->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->query->getQuery());
    }

    public function testSetAndGetShowMatch()
    {
        $this->query->setShowMatch(true);
        $this->assertTrue($this->query->getShowMatch());
    }
}

class TestAnalysisQuery extends AbstractQuery
{
    public function getType(): string
    {
        return null;
    }

    public function getRequestBuilder(): RequestBuilderInterface
    {
        return null;
    }

    public function getResponseParser(): ResponseParserInterface
    {
        return null;
    }
}
