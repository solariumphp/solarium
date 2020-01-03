<?php

declare(strict_types=1);

namespace Solarium\Builder\Analytics;

use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\ExpressionInterface;
use Solarium\Builder\FunctionInterface;
use Solarium\Builder\Value;
use Solarium\Exception\RuntimeException;

/**
 * Analytics Expression Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AnalyticsExpressionVisitor extends AbstractExpressionVisitor
{
    /**
     * @param \Solarium\Builder\ExpressionInterface $expr
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return mixed
     */
    public function dispatch(ExpressionInterface $expr)
    {
        if (true === $expr instanceof FunctionInterface) {
            return $this->walkExpression($expr);
        }

        throw new RuntimeException('Unknown Expression '.\get_class($expr));
    }

    /**
     * {@inheritdoc}
     */
    public function walkExpression(ExpressionInterface $expression)
    {
        return (string) $expression;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function walkValue(Value $value)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function walkCompositeExpression(ExpressionInterface $expr)
    {
    }
}
