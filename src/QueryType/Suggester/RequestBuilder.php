<?php

namespace Solarium\QueryType\Suggester;

use Solarium\Component\RequestBuilder\Suggester;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a Suggester query request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a Suggester query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);

        $componentRequestBuilder = new Suggester();
        $componentRequestBuilder->buildComponent($query, $request);

        return $request;
    }
}
