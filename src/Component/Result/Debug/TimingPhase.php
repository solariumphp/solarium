<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug timing phase result.
 */
class TimingPhase implements \IteratorAggregate, \Countable
{
    /**
     * Phase name.
     */
    protected string $name;

    /**
     * Phase time.
     */
    protected float $time;

    /**
     * Timing array.
     *
     * @var float[]
     */
    protected array $timings;

    /**
     * Constructor.
     *
     * @param string $name
     * @param float  $time
     * @param array  $timings
     */
    public function __construct(string $name, float $time, array $timings)
    {
        $this->name = $name;
        $this->time = $time;
        $this->timings = $timings;
    }

    /**
     * Get total time.
     *
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * Get a timing by key.
     *
     * @param mixed $key
     *
     * @return float|null
     */
    public function getTiming($key): ?float
    {
        return $this->timings[$key] ?? null;
    }

    /**
     * Get timings.
     *
     * @return float[]
     */
    public function getTimings(): array
    {
        return $this->timings;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->timings);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->timings);
    }
}
