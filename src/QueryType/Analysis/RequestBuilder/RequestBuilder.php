<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build an analysis request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for an analysis query.
     *
     * @param QueryInterface|AbstractQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        /** @var \Solarium\QueryType\Analysis\Query\AbstractQuery $query */
        $request = parent::build($query);
        $request->addParam('analysis.query', $query->getQuery());
        $request->addParam('analysis.showmatch', $query->getShowMatch());

        return $request;
    }
}
