<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats result trait.
 */
trait ResultTrait
{
    /**
     * Stats data.
     *
     * @var array
     */
    protected $stats;

    /**
     * Get min value.
     *
     * @return float|string|null
     */
    public function getMin()
    {
        return $this->getStatValue('min');
    }

    /**
     * Get max value.
     *
     * @return float|string|null
     */
    public function getMax()
    {
        return $this->getStatValue('max');
    }

    /**
     * Get sum value.
     *
     * @return float|null
     */
    public function getSum(): ?float
    {
        return $this->getStatValue('sum');
    }

    /**
     * Get count value.
     *
     * @return int|null
     */
    public function getCount(): ?int
    {
        return (int) $this->getStatValue('count');
    }

    /**
     * Get missing value.
     *
     * @return int|null
     */
    public function getMissing(): ?int
    {
        return $this->getStatValue('missing');
    }

    /**
     * Get sumOfSquares value.
     *
     * @return float|null
     */
    public function getSumOfSquares(): ?float
    {
        return $this->getStatValue('sumOfSquares');
    }

    /**
     * Get mean value.
     *
     * @return float|string|null
     */
    public function getMean()
    {
        return $this->getStatValue('mean');
    }

    /**
     * Get stddev value.
     *
     * @return float|null
     */
    public function getStddev(): ?float
    {
        return $this->getStatValue('stddev');
    }

    /**
     * Get percentiles.
     *
     * @return array|null
     */
    public function getPercentiles(): ?array
    {
        return $this->getStatValue('percentiles');
    }

    /**
     * Get the set of all distinct values.
     *
     * @return array|null
     */
    public function getDistinctValues(): ?array
    {
        return $this->getStatValue('distinctValues');
    }

    /**
     * Get the exact number of distinct values.
     *
     * @return int|null
     */
    public function getCountDistinct(): ?int
    {
        return $this->getStatValue('countDistinct');
    }

    /**
     * Get a statistical approximation of the number of distinct values.
     *
     * @return int|null
     */
    public function getCardinality(): ?int
    {
        return $this->getStatValue('cardinality');
    }

    /**
     * Get value by stat name.
     *
     * @param string $stat
     *
     * @return mixed|null
     */
    public function getStatValue(string $stat)
    {
        return $this->stats[$stat] ?? null;
    }
}
