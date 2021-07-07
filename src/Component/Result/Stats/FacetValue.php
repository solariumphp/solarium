<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats facet value.
 */
class FacetValue
{
    use ResultTrait;

    /**
     * Facet value.
     *
     * @var string
     */
    protected $value;

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
     * Get facet stats.
     *
     * @return array|null
     *
     * @deprecated Will be removed in Solarium 7
     */
    public function getFacets(): ?array
    {
        return null;
    }
}
