<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
