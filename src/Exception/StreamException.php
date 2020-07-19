<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Exception;

/**
 * StreamException exception for Solarium classes.
 */
class StreamException extends \UnexpectedValueException implements RuntimeExceptionInterface
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
    public function setExpression(string $expression): void
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
