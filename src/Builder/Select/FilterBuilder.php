<?php

declare(strict_types=1);

namespace Solarium\Builder\Select;

use Solarium\Builder\ExpressionInterface;

/**
 * Query Builder.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FilterBuilder
{
    /**
     * @var \Solarium\Builder\ExpressionInterface[]
     */
    private $expressions = [];

    /**
     * @var \Solarium\Builder\Select\ExpressionBuilder
     */
    private static $expressionBuilder;

    /**
     * @return static
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return \Solarium\Builder\Select\ExpressionBuilder
     */
    public static function expr(): ExpressionBuilder
    {
        if (null === self::$expressionBuilder) {
            self::$expressionBuilder = new ExpressionBuilder();
        }

        return self::$expressionBuilder;
    }

    /**
     * @param \Solarium\Builder\ExpressionInterface $comparison
     *
     * @return $this
     */
    public function where(ExpressionInterface $comparison): self
    {
        $this->expressions[] = $comparison;

        return $this;
    }

    /**
     * Convenience method for readability.
     *
     * @param \Solarium\Builder\ExpressionInterface $comparison
     *
     * @return $this
     */
    public function andWhere(ExpressionInterface $comparison): self
    {
        return $this->where($comparison);
    }

    /**
     * @return \Solarium\Builder\ExpressionInterface[]
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }
}
