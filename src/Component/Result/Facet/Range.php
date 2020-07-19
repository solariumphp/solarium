<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet;

use Solarium\Component\Result\Facet\Pivot\Pivot;

/**
 * Select range facet result.
 *
 * A multiquery facet will usually return a dataset of multiple count, in each
 * row a range as key and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 *
 * The extra counts 'before', 'between' and 'after' are only available if the
 * right settings for the option 'other' were used in the query.
 */
class Range extends Field
{
    /**
     * Count of all records with field values lower then lower bound of the first range.
     *
     * @var int
     */
    protected $before;

    /**
     * Count of all records with field values greater then the upper bound of the last range.
     *
     * @var int
     */
    protected $after;

    /**
     * Count all records with field values between the start and end bounds of all ranges.
     *
     * @var int
     */
    protected $between;

    /**
     * The lower bound of the ranges.
     *
     * @var string|int
     */
    protected $start;

    /**
     * The upper bound of all ranges.
     *
     * @var string|int
     */
    protected $end;

    /**
     * The gap between each range.
     *
     * @var string|int
     */
    protected $gap;

    /**
     * @var \Solarium\Component\Result\Facet\Pivot\Pivot|null
     */
    protected $pivot;

    /**
     * Constructor.
     *
     * @param array           $values
     * @param int|null        $before
     * @param int|null        $after
     * @param int|null        $between
     * @param string|int|null $start
     * @param string|int|null $end
     * @param string|int|null $gap
     * @param Pivot|null      $pivot
     */
    public function __construct(array $values, ?int $before, ?int $after, ?int $between, $start, $end, $gap, ?Pivot $pivot = null)
    {
        parent::__construct($values);

        $this->before = $before;
        $this->after = $after;
        $this->between = $between;
        $this->start = $start;
        $this->end = $end;
        $this->gap = $gap;
        $this->pivot = $pivot;
    }

    /**
     * Get 'before' count.
     *
     * Count of all records with field values lower then lower bound of the first range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int|null
     */
    public function getBefore(): ?int
    {
        return $this->before;
    }

    /**
     * Get 'after' count.
     *
     * Count of all records with field values greater then the upper bound of the last range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int|null
     */
    public function getAfter(): ?int
    {
        return $this->after;
    }

    /**
     * Get 'between' count.
     *
     * Count all records with field values between the start and end bounds of all ranges
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int|null
     */
    public function getBetween(): ?int
    {
        return $this->between;
    }

    /**
     * Get 'start' value of the ranges.
     *
     * The start value specified in the query facet.
     *
     * @return string
     */
    public function getStart(): string
    {
        return (string) $this->start;
    }

    /**
     * Get 'end' value of the ranges.
     *
     * The end value specified in the query facet
     *
     * @return string
     */
    public function getEnd(): string
    {
        return (string) $this->end;
    }

    /**
     * Get 'gap' between the start and end of each range.
     *
     * Get the gap specified in the query facet
     *
     * @return string
     */
    public function getGap(): string
    {
        return (string) $this->gap;
    }

    /**
     * @return \Solarium\Component\Result\Facet\Pivot\Pivot|null
     */
    public function getPivot(): ?Pivot
    {
        return $this->pivot;
    }
}
