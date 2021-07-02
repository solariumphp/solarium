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
 * Analytics result.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * @var \Solarium\Component\Result\Analytics\Expression[]
     */
    private $results = [];

    /**
     * @var \Solarium\Component\Result\Analytics\Grouping[]
     */
    private $groupings = [];

    /**
     * @param \Solarium\Component\Result\Analytics\Expression[] $results
     *
     * @return $this
     */
    public function setResults(array $results): self
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }

        return $this;
    }

    /**
     * @param \Solarium\Component\Result\Analytics\Expression $result
     *
     * @return $this
     */
    public function addResult(Expression $result): self
    {
        $this->results[$result->getName()] = $result;

        return $this;
    }

    /**
     * @return \Solarium\Component\Result\Analytics\Expression[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param string $name
     *
     * @return \Solarium\Component\Result\Analytics\Expression|null
     */
    public function getResult(string $name): ?Expression
    {
        return $this->results[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->results);
    }

    /**
     * @return \Solarium\Component\Result\Analytics\Grouping[]
     */
    public function getGroupings(): array
    {
        return $this->groupings;
    }

    /**
     * @param array $groupings
     *
     * @return $this
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
     * @return \Solarium\Component\Result\Analytics\Grouping|null
     */
    public function getGrouping(string $name): ?Grouping
    {
        return $this->groupings[$name] ?? null;
    }

    /**
     * @param \Solarium\Component\Result\Analytics\Grouping $grouping
     *
     * @return $this
     */
    public function addGrouping(Grouping $grouping): self
    {
        $this->groupings[$grouping->getName()] = $grouping;

        return $this;
    }
}
