<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Analytics;

/**
 * Expression.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Expression
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var float
     */
    private $value;

    /**
     * @param string $name
     * @param string $expression
     * @param float  $value
     */
    public function __construct(string $name, string $expression, float $value)
    {
        $this->name = $name;
        $this->expression = $expression;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
