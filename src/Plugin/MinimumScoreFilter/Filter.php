<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Exception\OutOfBoundsException;

/**
 * Minimumscore filter.
 */
class Filter
{
    /**
     * Apply filter to document array.
     *
     * @param \Solarium\QueryType\Select\Result\Document[] $documents
     * @param float                                        $maxScore
     * @param float                                        $ratio
     * @param string                                       $mode
     *
     * @return array
     */
    public function filterDocuments(array $documents, float $maxScore, float $ratio, string $mode): array
    {
        $threshold = $maxScore * $ratio;

        switch ($mode) {
            case Query::FILTER_MODE_REMOVE:
                foreach ($documents as $key => $document) {
                    if (($document->score ?? 0.0) < $threshold) {
                        unset($documents[$key]);
                    }
                }
                break;
            case Query::FILTER_MODE_MARK:
                foreach ($documents as $key => $document) {
                    $documents[$key] = new Document($document, $threshold);
                }
                break;
            default:
                throw new OutOfBoundsException(sprintf('Unknown filter mode in query: %s', $mode));
                break;
        }

        return $documents;
    }
}
