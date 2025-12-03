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
     */
    protected float|int $value;

    /**
     * Constructor.
     *
     * @param float|int $value
     */
    public function __construct(float|int $value)
    {
        $this->value = $value;
    }

    /**
     * Get value.
     *
     * @return float|int
     */
    public function getValue(): float|int
    {
        return $this->value;
    }
}
