<?php

declare(strict_types=1);

namespace Solarium\Builder;

use Solarium\Exception\RuntimeException;

/**
 * Expression Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractExpressionVisitor
{
    /**
     * Converts a comparison expression into solr query language.
     *
     * @param \Solarium\Builder\ExpressionInterface $expression
     *
     * @return mixed
     */
    abstract public function walkExpression(ExpressionInterface $expression);

    /**
     * Converts a value expression into solr query part.
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
                throw new RuntimeException('Unknown Expression '.\get_class($expr));
        }
    }
}
