<?php

namespace Solarium\Component\Result\Facet\Pivot;

/**
 * Select field pivot result.
 */
class Pivot implements \IteratorAggregate, \Countable
{
    /**
     * Value array.
     *
     * @var array
     */
    protected $pivot = [];

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        foreach ($data as $pivotData) {
            $this->pivot[] = new PivotItem($pivotData);
        }
    }

    /**
     * Get pivot results.
     *
     * @return Pivot[]
     */
    public function getPivot()
    {
        return $this->pivot;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->pivot);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->pivot);
    }
}
