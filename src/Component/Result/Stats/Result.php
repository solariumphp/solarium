<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats field result item.
 */
class Result
{
    use ResultTrait;

    /**
     * Field name.
     *
     * @var string
     */
    protected $field;

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->field;
    }

    /**
     * Get facet stats.
     *
     * @return array|null
     */
    public function getFacets(): ?array
    {
        return $this->getStatValue('facets');
    }

    /**
     * Get value by stat name.
     *
     * @param string $stat
     *
     * @return mixed|null
     *
     * @deprecated Use getStatValue() instead
     */
    public function getValue(string $stat)
    {
        return $this->getStatValue($stat);
    }
}
