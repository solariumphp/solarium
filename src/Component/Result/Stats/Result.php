<?php

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats field result item.
 */
class Result
{
    /**
     * Field name.
     *
     * @var string
     */
    protected $field;

    /**
     * Stats data.
     *
     * @var array
     */
    protected $stats;

    /**
     * Constructor.
     *
     * @param string $field
     * @param array  $stats
     */
    public function __construct($field, $stats)
    {
        $this->field = $field;
        $this->stats = $stats;
    }

    /**
     * Get field name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->field;
    }

    /**
     * Get min value.
     *
     * @return string
     */
    public function getMin()
    {
        return $this->getValue('min');
    }

    /**
     * Get max value.
     *
     * @return string
     */
    public function getMax()
    {
        return $this->getValue('max');
    }

    /**
     * Get sum value.
     *
     * @return string
     */
    public function getSum()
    {
        return $this->getValue('sum');
    }

    /**
     * Get count value.
     *
     * @return string
     */
    public function getCount()
    {
        return $this->getValue('count');
    }

    /**
     * Get missing value.
     *
     * @return string
     */
    public function getMissing()
    {
        return $this->getValue('missing');
    }

    /**
     * Get sumOfSquares value.
     *
     * @return string
     */
    public function getSumOfSquares()
    {
        return $this->getValue('sumOfSquares');
    }

    /**
     * Get mean value.
     *
     * @return string
     */
    public function getMean()
    {
        return $this->getValue('mean');
    }

    /**
     * Get stddev value.
     *
     * @return string
     */
    public function getStddev()
    {
        return $this->getValue('stddev');
    }

    /**
     * Get facet stats.
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->getValue('facets');
    }

    /**
     * Get percentile stats.
     *
     * @return array
     */
    public function getPercentiles()
    {
        return $this->getValue('percentiles');
    }

    /**
     * Get value by name.
     *
     * @param mixed $name
     *
     * @return string
     */
    protected function getValue($name)
    {
        return isset($this->stats[$name]) ? $this->stats[$name] : null;
    }
}
