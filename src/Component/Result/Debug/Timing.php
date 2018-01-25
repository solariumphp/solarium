<?php

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
    public function __construct($time, $phases)
    {
        $this->time = $time;
        $this->phases = $phases;
    }

    /**
     * Get total time.
     *
     * @return float
     */
    public function getTime()
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
    public function getPhase($key)
    {
        if (isset($this->phases[$key])) {
            return $this->phases[$key];
        }
    }

    /**
     * Get timings.
     *
     * @return array
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->phases);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->phases);
    }
}
