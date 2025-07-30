<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Type;

/**
 * Filter base class.
 */
abstract class AbstractFilter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return self Provides fluent interface
     */
    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
