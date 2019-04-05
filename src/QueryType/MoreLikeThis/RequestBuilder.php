<?php

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Select\RequestBuilder as SelectRequestBuilder;

/**
 * Build a MoreLikeThis request.
 */
class RequestBuilder extends SelectRequestBuilder
{
    /**
     * Build request for a MoreLikeThis query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        // add mlt params to request
        $request->addParam('mlt.interestingTerms', $query->getInterestingTerms());
        $request->addParam('mlt.match.include', $query->getMatchInclude());
        $request->addParam('mlt.match.offset', $query->getMatchOffset());
        $request->addParam('mlt.fl', implode(',', $query->getMltFields()));
        $request->addParam('mlt.mintf', $query->getMinimumTermFrequency());
        $request->addParam('mlt.mindf', $query->getMinimumDocumentFrequency());
        $request->addParam('mlt.minwl', $query->getMinimumWordLength());
        $request->addParam('mlt.maxwl', $query->getMaximumWordLength());
        $request->addParam('mlt.maxqt', $query->getMaximumQueryTerms());
        $request->addParam('mlt.maxntp', $query->getMaximumNumberOfTokens());
        $request->addParam('mlt.boost', $query->getBoost());
        $request->addParam('mlt.qf', $query->getQueryFields());

        // convert query to stream if necessary
        if (true === $query->getQueryStream()) {
            $request->removeParam('q');
            $request->setRawData($query->getQuery());
            $request->setMethod(Request::METHOD_POST);
            $request->addHeader('Content-Type: text/plain; charset=utf-8');
        }

        return $request;
    }
}
