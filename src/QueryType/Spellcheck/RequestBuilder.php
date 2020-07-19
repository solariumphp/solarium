<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck;

use Solarium\Component\RequestBuilder\Spellcheck;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;

/**
 * Build a Spellcheck query request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a Spellcheck query.
     *
     * @param \Solarium\Core\Query\AbstractQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        $componentRequestBuilder = new Spellcheck();
        $componentRequestBuilder->buildComponent($query, $request);

        return $request;
    }
}
