<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Suggester;

use Solarium\Component\RequestBuilder\Suggester;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
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
     * @param QueryInterface|AbstractQuery|Query $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        $componentRequestBuilder = new Suggester();
        $componentRequestBuilder->buildComponent($query, $request);

        return $request;
    }
}
