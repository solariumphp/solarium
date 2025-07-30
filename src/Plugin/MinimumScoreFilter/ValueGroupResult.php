<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Component\Result\Grouping\ValueGroup as StandardValueGroup;

/**
 * MinimumScoreFilter ValueGroupResult.
 */
class ValueGroupResult extends StandardValueGroup
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
     * @param string|null $value
     * @param int         $numFound
     * @param int         $start
     * @param array       $documents
     * @param float|null  $maximumScore
     * @param Query       $query
     */
    public function __construct(?string $value, int $numFound, int $start, array $documents, ?float $maximumScore, Query $query)
    {
        $this->filterMode = $query->getFilterMode();
        $this->filterRatio = $query->getFilterRatio();

        // Use the maximumScore of the first group as maximum for all groups
        if (($maximumScore ?? 0.0) > self::$overallMaximumScore) {
            self::$overallMaximumScore = $maximumScore;
        }

        parent::__construct($value, $numFound, $start, $documents);
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
