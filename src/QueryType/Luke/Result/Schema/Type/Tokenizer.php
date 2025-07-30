<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Type;

/**
 * Tokenizer.
 */
class Tokenizer
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     *
     * @return self Provides fluent interface
     */
    public function setArgs(array $args): self
    {
        $this->args = $args;

        return $this;
    }

    public function __toString(): string
    {
        return $this->className;
    }
}
