<?php

declare(strict_types=1);

namespace Solarium\Tests\Builder\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\Comparison;
use Solarium\Builder\CompositeComparison;
use Solarium\Builder\ExpressionInterface;
use Solarium\Builder\Select\QueryBuilder;
use Solarium\Builder\Select\QueryExpressionVisitor;
use Solarium\Builder\Value;
use Solarium\Exception\RuntimeException;

/**
 * Select Query Builder Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryBuilderTest extends TestCase
{
    /**
     * @var \Solarium\Builder\Select\QueryExpressionVisitor
     */
    private $visitor;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->visitor = new QueryExpressionVisitor();
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testEquals(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->eq('foo', 'bar'));

        $this->assertSame('foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->eq('foo', date_create('2020-01-01', new \DateTimeZone('UTC'))));

        $this->assertSame('foo:[2020-01-01T00:00:00Z TO 2020-01-01T00:00:00Z]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testNullValue(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->eq('foo', null));

        $this->assertSame('foo:[* TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testDoesNotEqual(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->neq('foo', 'bar'));

        $this->assertSame('-foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->neq('foo', date_create('2020-01-01', new \DateTimeZone('UTC'))));

        $this->assertSame('-foo:[2020-01-01T00:00:00Z TO 2020-01-01T00:00:00Z]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testGreaterThan(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->gt('foo', 2));

        $this->assertSame('foo:{2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testGreaterThanEqual(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->gte('foo', 2));

        $this->assertSame('foo:[2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLowerThan(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->lt('foo', 2));

        $this->assertSame('foo:[* TO 2}', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLowerThanEqual(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->lte('foo', 2));

        $this->assertSame('foo:[* TO 2]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRange(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->range('foo', [2]));

        $this->assertSame('foo:[2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->range('foo', [2, 5]));

        $this->assertSame('foo:[2 TO 5]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRangeInvalidValue(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->range('foo', 'bar'));

        $this->expectException(RuntimeException::class);

        $this->visitor->dispatch($filter->getExpressions()[0]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testIn(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->in('foo', [2, 5]));

        $this->assertSame('foo:(2 OR 5)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->andWhere(QueryBuilder::expr()->in('foo', 'bar'));

        $this->assertSame('foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testNotIn(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->notIn('foo', [2, 5]));

        $this->assertSame('-foo:(2 OR 5)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->notIn('foo', 'bar'));

        $this->assertSame('-foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLike(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->like('title', ['*foo', 'bar*']));

        $this->assertSame('title:(*foo OR bar*)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->like('title', 'foo*'));

        $this->assertSame('title:foo*', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRegularExpression(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->regexp('title', '[0-9]{5}'));

        $this->assertSame('title:/[0-9]{5}/', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMatch(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->match('title', 'foo*'));

        $this->assertSame('title:foo*', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testEmpty(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->empty('title'));

        $this->assertSame('(*:* NOT title:*)', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCompositeAnd(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->andX(
                QueryBuilder::expr()->eq('title', 'foo'),
                QueryBuilder::expr()->in('description', ['bar', 'baz'])
            ));

        $this->assertSame('title:"foo" AND description:("bar" OR "baz")', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCompositeOr(): void
    {
        $filter = QueryBuilder::create()
            ->where(QueryBuilder::expr()->orX(
                QueryBuilder::expr()->eq('title', 'foo'),
                QueryBuilder::expr()->in('description', ['bar', 'baz'])
            ));

        $this->assertSame('title:"foo" OR description:("bar" OR "baz")', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVisitExpressions(): void
    {
        $expression = QueryBuilder::expr()->eq('title', 'foo');

        $this->assertSame('title:"foo"', $expression->visit($this->visitor));

        $compositeExpression = QueryBuilder::expr()->andX(
            QueryBuilder::expr()->eq('title', 'foo'),
            QueryBuilder::expr()->in('description', ['bar', 'baz'])
        );

        $this->assertSame('title:"foo" AND description:("bar" OR "baz")', $compositeExpression->visit($this->visitor));

        $value = new Value('foo');
        $this->assertSame('foo', $value->visit($this->visitor));
        $this->assertSame('foo', $this->visitor->dispatch($value));
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testInvalidCompositeExpressionWithValue(): void
    {
        $this->expectException(RuntimeException::class);

        new CompositeComparison(CompositeComparison::TYPE_OR, [new Value('foo')]);
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testInvalidCompositeExpressionWithObject(): void
    {
        $this->expectException(RuntimeException::class);

        new CompositeComparison(CompositeComparison::TYPE_AND, [new \DateTime()]);
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testUnknownExpression(): void
    {
        $this->expectException(RuntimeException::class);

        $this->visitor->dispatch(new ExpressionDummy());
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testUnknownCompositeComparison(): void
    {
        $comparison = new CompositeComparison('TO', [QueryBuilder::expr()->eq('title', 'foo')]);

        $this->expectException(RuntimeException::class);

        $this->visitor->walkCompositeExpression($comparison);
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testUnknownComparison(): void
    {
        $comparison = new Comparison('title', 'FOO', 'bar');

        $this->expectException(RuntimeException::class);

        $this->visitor->walkExpression($comparison);
    }
}

/**
 * ExpressionDummy.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ExpressionDummy implements ExpressionInterface
{
    /**
     * {@inheritdoc}
     */
    public function visit(AbstractExpressionVisitor $visitor)
    {
        return $visitor->walkExpression($this);
    }
}
