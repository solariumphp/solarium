<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Builder;

use Solarium\Exception\RuntimeException;

/**
 * Expression Visitor.
 *
 * @codeCoverageIgnore
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractExpressionVisitor
{
    /**
     * Converts a comparison expression into Solr query language.
     *
     * @param \Solarium\Builder\ExpressionInterface $expression
     *
     * @return mixed
     */
    abstract public function walkExpression(ExpressionInterface $expression);

    /**
     * Converts a value expression into Solr query part.
     *
     * @param \Solarium\Builder\Value $value
     *
     * @return mixed
     */
    abstract public function walkValue(Value $value);

    /**
     * @param \Solarium\Builder\ExpressionInterface $expr
     *
     * @return mixed
     */
    abstract public function walkCompositeExpression(ExpressionInterface $expr);

    /**
     * @param \Solarium\Builder\ExpressionInterface $expr
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return mixed
     */
    public function dispatch(ExpressionInterface $expr)
    {
        switch (true) {
            case $expr instanceof Comparison:
                return $this->walkExpression($expr);
            case $expr instanceof Value:
                return $this->walkValue($expr);
            case $expr instanceof CompositeComparison:
                return $this->walkCompositeExpression($expr);
            default:
                throw new RuntimeException(sprintf('Unknown Expression %s', \get_class($expr)));
        }
    }
}
