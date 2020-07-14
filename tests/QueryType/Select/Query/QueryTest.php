<?php

namespace Solarium\Tests\QueryType\Select\Query;

use Solarium\Builder\Select\QueryBuilder;
use Solarium\Builder\Select\QueryExpressionVisitor;
use Solarium\QueryType\Select\Query\Query;

class QueryTest extends AbstractQueryTest
{
    public function setUp(): void
    {
        $this->query = new Query();
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testSetFacetQueryFromQueryBuilder(): void
    {
        $visitor = new QueryExpressionVisitor();
        $builder = QueryBuilder::create()
            ->where(QueryBuilder::expr()->eq('foo', 'bar'));

        $this->query->addFilterQueriesFromQueryBuilder($builder);

        $value = $visitor->dispatch($builder->getExpressions()[0]);
        $filterQuery = $this->query->getFilterQuery(sha1($value));

        self::assertSame($value, $filterQuery->getQuery());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testSetMultipleFilterQueriesFromQueryBuilder(): void
    {
        $visitor = new QueryExpressionVisitor();
        $expr = QueryBuilder::expr();

        $builder = QueryBuilder::create()
            ->where($expr->eq('foo', 'bar'))
            ->andWhere($expr->eq('baz', 'qux'))
        ;

        $this->query->addFilterQueriesFromQueryBuilder($builder);

        $first = $visitor->dispatch($builder->getExpressions()[0]);
        $second = $visitor->dispatch($builder->getExpressions()[1]);

        self::assertArrayHasKey(sha1($first), $this->query->getFilterQueries());
        self::assertArrayHasKey(sha1($second), $this->query->getFilterQueries());
    }
}
