<?php

namespace Solarium\Component\Result\Facet;

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
     * @var string
     */
    protected $start;

    /**
     * The upper bound of all ranges.
     *
     * @var string
     */
    protected $end;

    /**
     * The gap between each range.
     *
     * @var string
     */
    protected $gap;

    /**
     * Constructor.
     *
     * @param array $values
     * @param int   $before
     * @param int   $after
     * @param int   $between
     * @param int   $start
     * @param int   $end
     * @param int   $gap
     */
    public function __construct($values, $before, $after, $between, $start, $end, $gap)
    {
        parent::__construct($values);
        $this->before = $before;
        $this->after = $after;
        $this->between = $between;
        $this->start = $start;
        $this->end = $end;
        $this->gap = $gap;
    }

    /**
     * Get 'before' count.
     *
     * Count of all records with field values lower then lower bound of the first range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * Get 'after' count.
     *
     * Count of all records with field values greater then the upper bound of the last range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * Get 'between' count.
     *
     * Count all records with field values between the start and end bounds of all ranges
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getBetween()
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
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get 'end' value of the ranges.
     *
     * The end value specified in the query facet
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get 'gap' between the start and end of each range.
     *
     * Get the gap specified in the query facet
     *
     * @return string
     */
    public function getGap()
    {
        return $this->gap;
    }
}
