<?php

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats facet value.
 */
class FacetValue
{
    /**
     * Facet value.
     *
     * @var string
     */
    protected $value;

    /**
     * Stats data.
     *
     * @var array
     */
    protected $stats;

    /**
     * Constructor.
     *
     * @param string $value
     * @param array  $stats
     */
    public function __construct($value, $stats)
    {
        $this->value = $value;
        $this->stats = $stats;
    }

    /**
     * Get facet value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get min value.
     *
     * @return string
     */
    public function getMin()
    {
        return $this->stats['min'];
    }

    /**
     * Get max value.
     *
     * @return string
     */
    public function getMax()
    {
        return $this->stats['max'];
    }

    /**
     * Get sum value.
     *
     * @return string
     */
    public function getSum()
    {
        return $this->stats['sum'];
    }

    /**
     * Get count value.
     *
     * @return string
     */
    public function getCount()
    {
        return $this->stats['count'];
    }

    /**
     * Get missing value.
     *
     * @return string
     */
    public function getMissing()
    {
        return $this->stats['missing'];
    }

    /**
     * Get sumOfSquares value.
     *
     * @return string
     */
    public function getSumOfSquares()
    {
        return $this->stats['sumOfSquares'];
    }

    /**
     * Get mean value.
     *
     * @return string
     */
    public function getMean()
    {
        return $this->stats['mean'];
    }

    /**
     * Get stddev value.
     *
     * @return string
     */
    public function getStddev()
    {
        return $this->stats['stddev'];
    }

    /**
     * Get facet stats.
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->stats['facets'];
    }
}
