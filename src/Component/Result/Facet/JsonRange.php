<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet;

/**
 * JsonRange field facet result.
 *
 * The range values are a dataset of multiple rows, in each row a
 * value and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 * The additional properties of before, after, and between are only avilable if the initial request has the 'other' param set.
 * See https://solr.apache.org/guide/json-facet-api.html#range-facet-parameters
 */
class JsonRange extends Buckets
{
    /**
     * Count of all records with field values lower then lower bound of the first range.
     *
     * @var int|null
     */
    protected $before;

    /**
     * Count of all records with field values greater then the upper bound of the last range.
     *
     * @var int|null
     */
    protected $after;

    /**
     * Count all records with field values between the start and end bounds of all ranges.
     *
     * @var int|null
     */
    protected $between;

    /**
     * Constructor.
     *
     * @param Bucket[] $buckets
     * @param int|null $before
     * @param int|null $after
     * @param int|null $between
     */
    public function __construct(array $buckets, ?int $before, ?int $after, ?int $between)
    {
        parent::__construct($buckets, null);

        $this->before = $before;
        $this->after = $after;
        $this->between = $between;
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
}
