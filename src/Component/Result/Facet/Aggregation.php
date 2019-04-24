<?php

namespace Solarium\Component\Result\Facet;

/**
 * Aggregation.
 */
class Aggregation implements FacetResultInterface
{
    /**
     * Value.
     *
     * @var float|int
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param float $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get value.
     *
     * @return float|int
     */
    public function getValue()
    {
        return $this->value;
    }
}
