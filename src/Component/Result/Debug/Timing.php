<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug timing result.
 */
class Timing implements \IteratorAggregate, \Countable
{
    /**
     * Time.
     *
     * @var float
     */
    protected $time;

    /**
     * Timing phase array.
     *
     * @var array
     */
    protected $phases;

    /**
     * Constructor.
     *
     * @param float $time
     * @param array $phases
     */
    public function __construct(float $time, array $phases)
    {
        $this->time = $time;
        $this->phases = $phases;
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
     * Get a timing phase by key.
     *
     * @param mixed $key
     *
     * @return TimingPhase|null
     */
    public function getPhase($key): ?TimingPhase
    {
        return $this->phases[$key] ?? null;
    }

    /**
     * Get timings.
     *
     * @return array
     */
    public function getPhases(): array
    {
        return $this->phases;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->phases);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->phases);
    }
}
