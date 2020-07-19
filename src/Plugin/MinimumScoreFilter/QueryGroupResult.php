<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Component\Result\Grouping\QueryGroup as StandardQueryGroupResult;

/**
 * MinimumScoreFilter QueryGroupResult.
 */
class QueryGroupResult extends StandardQueryGroupResult
{
    /**
     * @var float
     */
    protected static $overallMaximumScore;

    /**
     * @var string
     */
    protected $filterMode;

    /**
     * @var float
     */
    protected $filterRatio;

    /**
     * @var bool
     */
    protected $filtered = false;

    /**
     * Constructor.
     *
     * @param int   $matches
     * @param int   $numFound
     * @param int   $start
     * @param float $maximumScore
     * @param array $documents
     * @param Query $query
     */
    public function __construct(int $matches, int $numFound, int $start, float $maximumScore, array $documents, Query $query)
    {
        $this->filterMode = $query->getFilterMode();
        $this->filterRatio = $query->getFilterRatio();

        // Use the maximumScore of the first group as maximum for all groups
        if (null === self::$overallMaximumScore) {
            self::$overallMaximumScore = $maximumScore;
        }

        parent::__construct($matches, $numFound, $start, $maximumScore, $documents, $query);
    }

    /**
     * Get all documents, apply filter at first use.
     *
     * @return array
     */
    public function getDocuments(): array
    {
        if (!$this->filtered) {
            $filter = new Filter();
            $this->documents = $filter->filterDocuments($this->documents, self::$overallMaximumScore, $this->filterRatio, $this->filterMode);
            $this->filtered = true;
        }

        return $this->documents;
    }
}
