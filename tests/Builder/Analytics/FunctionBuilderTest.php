<?php

declare(strict_types=1);

namespace Solarium\Tests\Builder\Analytics;

use PHPUnit\Framework\TestCase;
use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\Analytics\AnalyticsExpressionVisitor;
use Solarium\Builder\Analytics\FunctionBuilder;
use Solarium\Builder\ExpressionInterface;
use Solarium\Exception\RuntimeException;

/**
 * FunctionBuilderTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FunctionBuilderTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     *
     * @see https://solr.apache.org/guide/analytics.html#example-construction
     */
    public function testBuilder(): void
    {
        $expr = FunctionBuilder::expr();
        $builder = FunctionBuilder::create()
            ->where($expr->div(
                $expr->sum(
                    'a',
                    $expr->fillMissing('b', 0)
                ),
                $expr->add(
                    10.5,
                    $expr->count(
                        $expr->mult('a', 'c')
                    )
                )
            ));

        $this->assertSame('div(sum(a,fill_missing(b,0)),add(10.5,count(mult(a,c))))', (string) $builder->getFunction());
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCompositeReductionFunction(): void
    {
        $this->expectException(RuntimeException::class);
        $expr = FunctionBuilder::expr();

        FunctionBuilder::create()
            ->where($expr->count(
                $expr->missing('foo')
            ))
        ;
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVisitExpressions(): void
    {
        $mapping = FunctionBuilder::expr()->mult(3.5, [1, -4]);

        $visitor = new AnalyticsExpressionVisitor();
        $this->assertSame('mult(3.5,[1,-4])', $mapping->visit($visitor));

        $reduction = FunctionBuilder::expr()->count('foo');
        $this->assertSame('count(foo)', $reduction->visit($visitor));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVisitor(): void
    {
        $visitor = new AnalyticsExpressionVisitor();
        $reduction = FunctionBuilder::expr()->count('foo');

        $this->assertSame('count(foo)', $visitor->dispatch($reduction));
    }

    /**
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVisitUnsupportedExpression(): void
    {
        $visitor = new AnalyticsExpressionVisitor();
        $expression = new ExpressionDummy();

        $this->expectException(RuntimeException::class);
        $visitor->dispatch($expression);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMultiply(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->mult(3.5, [1, -4]));

        $this->assertSame('mult(3.5,[1,-4])', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testCount(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->count('foo'));

        $this->assertSame('count(foo)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testDocCount(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->docCount('foo'));

        $this->assertSame('doc_count(foo)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMissing(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->missing('foo', 'bar'));

        $this->assertSame('missing(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testUnique(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->unique('foo', 'bar'));

        $this->assertSame('unique(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testSum(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->sum('foo', 'bar'));

        $this->assertSame('sum(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testVariance(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->variance('foo', 'bar'));

        $this->assertSame('variance(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testStddev(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->stddev('foo', 'bar'));

        $this->assertSame('stddev(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMean(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->mean('foo', 'bar'));

        $this->assertSame('mean(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testWeightedMean(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->weightedMean('foo', 'bar'));

        $this->assertSame('wmean(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMinimum(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->minimum('foo', 'bar'));

        $this->assertSame('min(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMaximum(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->maximum('foo', 'bar'));

        $this->assertSame('max(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testMedian(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->median('foo', 'bar'));

        $this->assertSame('median(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testPercentile(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->percentile('foo', 'bar'));

        $this->assertSame('percentile(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\RuntimeException
     */
    public function testOrdinal(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->ordinal('foo', 'bar'));

        $this->assertSame('ordinal(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNegation(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->negation('foo', 'bar'));

        $this->assertSame('neg(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAbsolute(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->absolute('foo', 'bar'));

        $this->assertSame('abs(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRound(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->round('foo'));

        $this->assertSame('round(foo)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCeil(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->ceil('foo'));

        $this->assertSame('ceil(foo)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFloor(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->floor('foo', 'bar'));

        $this->assertSame('floor(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAdd(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->add('foo', 'bar'));

        $this->assertSame('add(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSub(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->sub('foo', 'bar'));

        $this->assertSame('sub(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMult(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->mult('foo', 'bar'));

        $this->assertSame('mult(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDiv(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->div('foo', 'bar'));

        $this->assertSame('div(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPower(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->power('foo', 'bar'));

        $this->assertSame('pow(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLogarithm(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->logarithm('foo', 'bar'));

        $this->assertSame('log(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAnd(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->and('foo', 'bar'));

        $this->assertSame('and(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOr(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->or('foo', 'bar'));

        $this->assertSame('or(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExists(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->exists('foo', 'bar'));

        $this->assertSame('exists(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEqual(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->equal('foo', 'bar'));

        $this->assertSame('equal(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGt(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->gt('foo', 'bar'));

        $this->assertSame('gt(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGte(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->gte('foo', 'bar'));

        $this->assertSame('gte(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLt(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->lt('foo', 'bar'));

        $this->assertSame('lt(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLte(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->lte('foo', 'bar'));

        $this->assertSame('lte(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testTop(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->top('foo', 'bar'));

        $this->assertSame('top(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testBottom(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->bottom('foo', 'bar'));

        $this->assertSame('bottom(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIf(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->if('foo', 'bar'));

        $this->assertSame('if(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testReplace(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->replace('foo', 'bar'));

        $this->assertSame('replace(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFillMissing(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->fillMissing('foo', 'bar'));

        $this->assertSame('fill_missing(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRemove(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->remove('foo', 'bar'));

        $this->assertSame('remove(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFilter(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->filter('foo', 'bar'));

        $this->assertSame('filter(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDate(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->date('foo', 'bar'));

        $this->assertSame('date(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDateMath(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->dateMath('foo', 'bar'));

        $this->assertSame('date_math(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testString(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->string('foo', 'bar'));

        $this->assertSame('string(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConcat(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->concat('foo', 'bar'));

        $this->assertSame('concat(foo,bar)', (string) $builder->getFunction());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConcatSeparated(): void
    {
        $builder = FunctionBuilder::create()
            ->where(FunctionBuilder::expr()->concatSeparated('foo', 'bar'));

        $this->assertSame('concat_sep(foo,bar)', (string) $builder->getFunction());
    }
}

/**
 * Expression Dummy.
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
