<?php

namespace Solarium\QueryType\Ping;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a ping request.
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

        return $request;
    }
}
