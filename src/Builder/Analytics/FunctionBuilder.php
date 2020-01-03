<?php

declare(strict_types=1);

namespace Solarium\Builder\Analytics;

use Solarium\Builder\ExpressionInterface;

/**
 * FunctionBuilder.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FunctionBuilder
{
    /**
     * @var \Solarium\Builder\ExpressionInterface
     */
    private $function;

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
     * @return \Solarium\Builder\Analytics\ExpressionBuilder
     */
    public static function expr(): ExpressionBuilder
    {
        if (null === self::$expressionBuilder) {
            self::$expressionBuilder = new ExpressionBuilder();
        }

        return self::$expressionBuilder;
    }

    /**
     * @param \Solarium\Builder\ExpressionInterface $function
     *
     * @return $this
     */
    public function where(ExpressionInterface $function): self
    {
        $this->function = $function;

        return $this;
    }

    /**
     * @return \Solarium\Builder\ExpressionInterface
     */
    public function getFunction(): ExpressionInterface
    {
        return $this->function;
    }
}
