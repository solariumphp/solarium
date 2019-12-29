<?php

declare(strict_types=1);

namespace Solarium\Tests\Builder\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\Comparison;
use Solarium\Builder\CompositeComparison;
use Solarium\Builder\ExpressionInterface;
use Solarium\Builder\Select\FilterBuilder;
use Solarium\Builder\Select\QueryExpressionVisitor;
use Solarium\Builder\Value;

/**
 * Select Query Builder Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SelectQueryBuilderTest extends TestCase
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
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->eq('foo', 'bar'));

        $this->assertSame('foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->eq('foo', date_create('2020-01-01')));

        $this->assertSame('foo:[2020-01-01T00:00:00Z TO 2020-01-01T00:00:00Z]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testNullValue(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->eq('foo', null));

        $this->assertSame('foo:[* TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testDoesNotEqual(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->neq('foo', 'bar'));

        $this->assertSame('-foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->neq('foo', date_create('2020-01-01')));

        $this->assertSame('-foo:[2020-01-01T00:00:00Z TO 2020-01-01T00:00:00Z]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testGreaterThan(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->gt('foo', 2));

        $this->assertSame('foo:{2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testGreaterThanEqual(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->gte('foo', 2));

        $this->assertSame('foo:[2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLowerThan(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->lt('foo', 2));

        $this->assertSame('foo:[* TO 2}', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLowerThanEqual(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->lte('foo', 2));

        $this->assertSame('foo:[* TO 2]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRange(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->range('foo', [2]));

        $this->assertSame('foo:[2 TO *]', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->range('foo', [2, 5]));

        $this->assertSame('foo:[2 TO 5]', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRangeInvalidValue(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->range('foo', 'bar'));

        $this->expectException(RuntimeException::class);

        $this->visitor->dispatch($filter->getExpressions()[0]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testIn(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->in('foo', [2, 5]));

        $this->assertSame('foo:(2 OR 5)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->andWhere(FilterBuilder::expr()->in('foo', 'bar'));

        $this->assertSame('foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testNotIn(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->notIn('foo', [2, 5]));

        $this->assertSame('-foo:(2 OR 5)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->notIn('foo', 'bar'));

        $this->assertSame('-foo:"bar"', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testLike(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->like('title', ['*foo', 'bar*']));

        $this->assertSame('title:(*foo OR bar*)', $this->visitor->dispatch($filter->getExpressions()[0]));

        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->like('title', 'foo*'));

        $this->assertSame('title:foo*', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testRegularExpression(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->regexp('title', '[0-9]{5}'));

        $this->assertSame('title:/[0-9]{5}/', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMatch(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->match('title', 'foo*'));

        $this->assertSame('title:foo*', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCompositeAnd(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->andX(
                FilterBuilder::expr()->eq('title', 'foo'),
                FilterBuilder::expr()->in('description', ['bar', 'baz'])
            ));

        $this->assertSame('title:"foo" AND description:("bar" OR "baz")', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCompositeOr(): void
    {
        $filter = FilterBuilder::create()
            ->where(FilterBuilder::expr()->orX(
                FilterBuilder::expr()->eq('title', 'foo'),
                FilterBuilder::expr()->in('description', ['bar', 'baz'])
            ));

        $this->assertSame('title:"foo" OR description:("bar" OR "baz")', $this->visitor->dispatch($filter->getExpressions()[0]));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVisitExpressions(): void
    {
        $expression = FilterBuilder::expr()->eq('title', 'foo');

        $this->assertSame('title:"foo"', $expression->visit($this->visitor));

        $compositeExpression = FilterBuilder::expr()->andX(
            FilterBuilder::expr()->eq('title', 'foo'),
            FilterBuilder::expr()->in('description', ['bar', 'baz'])
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
        $comparison = new CompositeComparison('TO', [FilterBuilder::expr()->eq('title', 'foo')]);

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
