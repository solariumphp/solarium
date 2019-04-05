<?php

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Result\Document as ReadOnlyDocument;
use Solarium\QueryType\Select\Result\Result as SelectResult;

/**
 * MoreLikeThis query result.
 *
 * This is the standard resulttype for a moreLikeThis query. Example usage:
 * <code>
 * // total solr mlt results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // iterate over fetched mlt docs
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
     * this will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top tf/idf terms. NOTE: if you select 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost=true all terms will have boost=1.0
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function getInterestingTerms()
    {
        $query = $this->getQuery();
        if ('none' == $query->getInterestingTerms()) {
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
