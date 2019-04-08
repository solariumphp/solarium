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
    public function __construct(string $field, array $stats)
    {
        $this->field = $field;
        $this->stats = $stats;
    }

    /**
     * Get field name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->field;
    }

    /**
     * Get min value.
     *
     * @return string
     */
    public function getMin(): string
    {
        return $this->getValue('min');
    }

    /**
     * Get max value.
     *
     * @return string
     */
    public function getMax(): string
    {
        return $this->getValue('max');
    }

    /**
     * Get sum value.
     *
     * @return string
     */
    public function getSum(): string
    {
        return $this->getValue('sum');
    }

    /**
     * Get count value.
     *
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->getValue('count');
    }

    /**
     * Get missing value.
     *
     * @return string
     */
    public function getMissing(): string
    {
        return $this->getValue('missing');
    }

    /**
     * Get sumOfSquares value.
     *
     * @return string
     */
    public function getSumOfSquares(): string
    {
        return $this->getValue('sumOfSquares');
    }

    /**
     * Get mean value.
     *
     * @return string
     */
    public function getMean(): string
    {
        return $this->getValue('mean');
    }

    /**
     * Get stddev value.
     *
     * @return string
     */
    public function getStddev(): string
    {
        return $this->getValue('stddev');
    }

    /**
     * Get facet stats.
     *
     * @return array
     */
    public function getFacets(): array
    {
        return $this->getValue('facets');
    }

    /**
     * Get percentile stats.
     *
     * @return array
     */
    public function getPercentiles(): array
    {
        return $this->getValue('percentiles');
    }

    /**
     * Get value by name.
     *
     * @param mixed $name
     *
     * @return string|array|null
     */
    protected function getValue($name)
    {
        return $this->stats[$name] ?? null;
    }
}
