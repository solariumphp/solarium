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
use Solarium\Builder\FunctionInterface;

/**
 * FunctionBuilder.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FunctionBuilder
{
    private ExpressionInterface&FunctionInterface $function;

    private static ?ExpressionBuilder $expressionBuilder = null;

    /**
     * @return static
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return ExpressionBuilder
     */
    public static function expr(): ExpressionBuilder
    {
        if (null === self::$expressionBuilder) {
            self::$expressionBuilder = new ExpressionBuilder();
        }

        return self::$expressionBuilder;
    }

    /**
     * @param ExpressionInterface&FunctionInterface $function
     *
     * @return self Provides fluent interface
     */
    public function where(ExpressionInterface&FunctionInterface $function): self
    {
        $this->function = $function;

        return $this;
    }

    /**
     * @return ExpressionInterface&FunctionInterface
     */
    public function getFunction(): ExpressionInterface&FunctionInterface
    {
        return $this->function;
    }
}
