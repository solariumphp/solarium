<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a Luke request.
 */
class RequestBuilder extends AbstractRequestBuilder
{
    /**
     * Build request for a Luke query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        // add luke params to request
        $fields = $query->getFields();

        $request->addParam('show', $query->getShow());
        $request->addParam('id', $query->getId());
        $request->addParam('docId', $query->getDocId());
        $request->addParam('fl', 0 === \count($fields) ? null : implode(',', $fields));
        $request->addParam('numTerms', $query->getNumTerms());
        $request->addParam('includeIndexFieldFlags', $query->getIncludeIndexFieldFlags());

        return $request;
    }
}
