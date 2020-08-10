<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Api;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\Api\Query as ApiQuery;

/**
 * Build an API request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a API query.
     *
     * @param QueryInterface|ApiQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        $request->setMethod($query->getMethod());
        $request->setApi($query->getVersion());
        $request->setIsServerRequest(true);

        if ($accept = $query->getAccept()) {
            $request->addHeader('Accept: '.$accept);
        }
        if ($contentType = $query->getContentType()) {
            $request->addHeader('Content-Type: '.$contentType);
        }
        if ($rawData = $query->getRawData()) {
            $request->setRawData($rawData);
        }

        return $request;
    }
}
