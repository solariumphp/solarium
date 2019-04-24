<?php

namespace Solarium\QueryType\RealtimeGet;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a RealtimeGet request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a ping query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_GET);
        $request->addParam('ids', implode(',', $query->getIds()));

        return $request;
    }
}
