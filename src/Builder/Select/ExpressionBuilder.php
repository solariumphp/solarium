<?php

declare(strict_types=1);

namespace Solarium\Builder\Select;

use Solarium\Builder\Comparison;
use Solarium\Builder\CompositeComparison;

/**
 * Expression Builder.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ExpressionBuilder
{
    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\CompositeComparison
     */
    public function andX($x = null): CompositeComparison
    {
        return new CompositeComparison(CompositeComparison::TYPE_AND, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\CompositeComparison
     */
    public function orX($x = null): CompositeComparison
    {
        return new CompositeComparison(CompositeComparison::TYPE_OR, \func_get_args());
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function eq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::EQ, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function neq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::NEQ, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function lt(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::LT, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function gt(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GT, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function lte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::LTE, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function gte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GTE, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function in(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::IN, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function notIn(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::NIN, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function range(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::RANGE, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function regexp(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::REGEXP, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function like(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::LIKE, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return \Solarium\Builder\Comparison
     */
    public function match(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::MATCH, $value);
    }
}
