<?php

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
    public function __construct($matches, $numFound, $start, $maximumScore, $documents, $query)
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
    public function getDocuments()
    {
        if (!$this->filtered) {
            $filter = new Filter();
            $this->documents = $filter->filterDocuments($this->documents, self::$overallMaximumScore, $this->filterRatio, $this->filterMode);
            $this->filtered = true;
        }

        return $this->documents;
    }
}
