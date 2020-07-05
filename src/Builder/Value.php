<?php

declare(strict_types=1);

namespace Solarium\Builder;

/**
 * Value.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Value implements ExpressionInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(AbstractExpressionVisitor $visitor)
    {
        return $visitor->walkValue($this);
    }
}
