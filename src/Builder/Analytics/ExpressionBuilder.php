<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Builder\Analytics;

use Solarium\Builder\ExpressionInterface;

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
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function count($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::COUNT, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function docCount($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::DOC_COUNT, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function missing($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::MISSING, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function unique($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::UNIQUE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function sum($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::SUM, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function variance($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::VARIANCE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function stddev($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::STANDARD_DEVIATION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function mean($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::MEAN, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function weightedMean($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::WEIGHTED_MEAN, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function minimum($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::MINIMUM, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function maximum($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::MAXIMUM, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function median($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::MEDIAN, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function percentile($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::PERCENTILE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function ordinal($x = null): ExpressionInterface
    {
        return new ReductionFunction(ReductionFunction::ORDINAL, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function negation($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::NEGATION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function absolute($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::ABSOLUTE_VALUE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function round($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::ROUND, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function ceil($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::CEILING, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function floor($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::FLOOR, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function add($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::ADDITION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function sub($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::SUBTRACTION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function mult($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::MULTIPLICATION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function div($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::DIVISION, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function power($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::POWER, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function logarithm($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::LOGARITHM, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function and($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::AND, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function or($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::OR, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function exists($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::EXISTS, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function equal($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::EQUAL, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function gt($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::GREATER_THAN, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function gte($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::GREATER_THAN_EQUALS, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function lt($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::LESS_THAN, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function lte($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::LESS_THAN_EQUALS, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function top($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::TOP, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function bottom($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::BOTTOM, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function if($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::IF, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function replace($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::REPLACE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function fillMissing($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::FILL_MISSING, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function remove($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::REMOVE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function filter($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::FILTER, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function date($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::DATE_PARSE, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function dateMath($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::DATE_MATH, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function string($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::STRING, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function concat($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::CONCAT, \func_get_args());
    }

    /**
     * @param null $x
     *
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function concatSeparated($x = null): ExpressionInterface
    {
        return new MappingFunction(MappingFunction::CONCAT_SEPARATED, \func_get_args());
    }
}
