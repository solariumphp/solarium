<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Builder;

/**
 * Comparison.
 *
 * @codeCoverageIgnore
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Comparison implements ExpressionInterface
{
    /**
     * Equals.
     */
    public const EQ = '=';

    /**
     * Does not equal.
     */
    public const NEQ = '<>';

    /**
     * Less than.
     */
    public const LT = '<';

    /**
     * Less than or equal to.
     */
    public const LTE = '<=';

    /**
     * Greater than.
     */
    public const GT = '>';

    /**
     * Greater than or equal to.
     */
    public const GTE = '>=';

    /**
     * In.
     */
    public const IN = 'IN';

    /**
     * Not in.
     */
    public const NIN = 'NIN';

    /**
     * Range.
     */
    public const RANGE = 'RANGE';

    /**
     * Regular expression.
     */
    public const REGEXP = 'REGEXP';

    /**
     * Like.
     */
    public const LIKE = 'LIKE';

    /**
     * Matching.
     */
    public const MATCH = 'MATCH';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $field
     * @param string $operator
     * @param mixed  $value
     */
    public function __construct(string $field, string $operator, $value)
    {
        if (!($value instanceof Value)) {
            $value = new Value($value);
        }

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return \Solarium\Builder\Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(AbstractExpressionVisitor $visitor)
    {
        return $visitor->walkExpression($this);
    }
}
