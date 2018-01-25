<?php

namespace Solarium\QueryType\Terms;

use Solarium\Component\RequestBuilder\Terms;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a Terms query request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a Terms query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);

        $componentRequestBuilder = new Terms();
        $componentRequestBuilder->buildComponent($query, $request);

        return $request;
    }
}
