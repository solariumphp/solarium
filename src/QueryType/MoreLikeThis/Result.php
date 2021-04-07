<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Result\Document as ReadOnlyDocument;
use Solarium\QueryType\Select\Result\Result as SelectResult;

/**
 * MoreLikeThis query result.
 *
 * This is the standard resulttype for a MoreLikeThis query. Example usage:
 * <code>
 * // total Solr MLT results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // iterate over fetched MLT docs
 * foreach ($result as $doc) {
 *    ....
 * }
 * </code>
 */
class Result extends SelectResult
{
    /**
     * MLT interesting terms.
     */
    protected $interestingTerms;

    /**
     * MLT match document.
     */
    protected $match;

    /**
     * Get MLT interesting terms.
     *
     * This will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top TF/IDF terms.
     *
     * If mlt.interestingTerms was 'list', a flat list is returned.
     *
     * If mlt.interestingTerms was 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost was true all terms will have boost=1.0.
     *
     * If mlt.interestingTerms was 'none', the terms aren't available
     * and an exception is thrown.
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function getInterestingTerms()
    {
        $query = $this->getQuery();
        if ('none' === $query->getInterestingTerms()) {
            throw new UnexpectedValueException('interestingterms is none');
        }
        $this->parseResponse();

        return $this->interestingTerms;
    }

    /**
     * Get matched document.
     *
     * Only available if matchinclude was set to true in the query.
     *
     * @throws UnexpectedValueException
     *
     * @return ReadOnlyDocument
     */
    public function getMatch()
    {
        $query = $this->getQuery();
        if (true !== $query->getMatchInclude()) {
            throw new UnexpectedValueException('matchinclude was disabled in the MLT query');
        }
        $this->parseResponse();

        return $this->match;
    }
}
