<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Analytics;

use Solarium\Component\Result\ComponentResultInterface;

/**
 * Analytics result.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Result implements ComponentResultInterface, \IteratorAggregate, \Countable
{
    /**
     * @var Expression[]
     */
    private array $results = [];

    /**
     * @var Grouping[]
     */
    private array $groupings = [];

    /**
     * @param Expression[] $results
     *
     * @return self Provides fluent interface
     */
    public function setResults(array $results): self
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }

        return $this;
    }

    /**
     * @param Expression $result
     *
     * @return self Provides fluent interface
     */
    public function addResult(Expression $result): self
    {
        $this->results[$result->getName()] = $result;

        return $this;
    }

    /**
     * @return Expression[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param string $name
     *
     * @return Expression|null
     */
    public function getResult(string $name): ?Expression
    {
        return $this->results[$name] ?? null;
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

    /**
     * @return Grouping[]
     */
    public function getGroupings(): array
    {
        return $this->groupings;
    }

    /**
     * @param Grouping[] $groupings
     *
     * @return self Provides fluent interface
     */
    public function setGroupings(array $groupings): self
    {
        foreach ($groupings as $grouping) {
            $this->addGrouping($grouping);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Grouping|null
     */
    public function getGrouping(string $name): ?Grouping
    {
        return $this->groupings[$name] ?? null;
    }

    /**
     * @param Grouping $grouping
     *
     * @return self Provides fluent interface
     */
    public function addGrouping(Grouping $grouping): self
    {
        $this->groupings[$grouping->getName()] = $grouping;

        return $this;
    }
}
