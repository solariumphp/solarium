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
    public function __construct(string $value, array $stats)
    {
        $this->value = $value;
        $this->stats = $stats;
    }

    /**
     * Get facet value.
     *
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Get min value.
     *
     * @return float|null
     */
    public function getMin(): ?float
    {
        return $this->stats['min'];
    }

    /**
     * Get max value.
     *
     * @return float|null
     */
    public function getMax(): ?float
    {
        return $this->stats['max'];
    }

    /**
     * Get sum value.
     *
     * @return float
     */
    public function getSum(): float
    {
        return $this->stats['sum'];
    }

    /**
     * Get count value.
     *
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->stats['count'];
    }

    /**
     * Get missing value.
     *
     * @return string
     */
    public function getMissing(): string
    {
        return $this->stats['missing'];
    }

    /**
     * Get sumOfSquares value.
     *
     * @return float
     */
    public function getSumOfSquares(): float
    {
        return $this->stats['sumOfSquares'];
    }

    /**
     * Get mean value.
     *
     * @return string|float
     */
    public function getMean()
    {
        return $this->stats['mean'];
    }

    /**
     * Get stddev value.
     *
     * @return float
     */
    public function getStddev(): float
    {
        return $this->stats['stddev'];
    }

    /**
     * Get facet stats.
     *
     * @return array
     */
    public function getFacets(): array
    {
        return $this->stats['facets'];
    }
}
