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
 * Facet response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Facet implements \IteratorAggregate, \Countable
{
    private ?string $pivot;

    private string $value;

    /**
     * @var Expression[]
     */
    private array $results;

    /**
     * @var Facet[]
     */
    private array $children = [];

    /**
     * @param string      $value
     * @param string|null $pivot
     */
    public function __construct(string $value, ?string $pivot = null)
    {
        $this->value = $value;
        $this->pivot = $pivot;
    }

    /**
     * @return string|null
     */
    public function getPivot(): ?string
    {
        return $this->pivot;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return Expression[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param Expression $expression
     *
     * @return self Provides fluent interface
     */
    public function addResult(Expression $expression): self
    {
        $this->results[$expression->getName()] = $expression;

        return $this;
    }

    /**
     * @return Facet[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Facet $facet
     *
     * @return self Provides fluent interface
     */
    public function addChild(Facet $facet): self
    {
        $this->children[] = $facet;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->results);
    }
}
