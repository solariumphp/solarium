<?php

namespace Solarium\Exception;

/**
 * StreamException exception for Solarium classes.
 */
class StreamException extends \UnexpectedValueException implements ExceptionInterface
{
    /**
     * @var string the streaming expression
     */
    protected $expression = '';

    /**
     * Set the streaming expression that caused the exception.
     *
     * @param string $expression
     */
    public function setExpression(string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Get the streaming expression that caused the exception.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }
}
