<?php

namespace Solarium\QueryType\Server\V2Api;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\V2Api\Query as V2ApiQuery;

/**
 * Build a V2 API request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a V2 API query.
     *
     * @param QueryInterface|V2ApiQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);
        $request->setMethod($query->getMethod());
        $request->setApi(Request::API_V2);
        return $request;
    }

}
