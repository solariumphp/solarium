<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Metrics;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a metrics request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a metrics query.
     *
     * @param QueryInterface&Query $query
     *
     * @return Request
     */
    public function build(QueryInterface|Query $query): Request
    {
        $request = parent::build($query);

        $request->setIsServerRequest(true);

        if (null !== $name = $query->getName()) {
            $request->addParam('name', implode(',', $name));
        }

        if (null !== $category = $query->getCategory()) {
            $request->addParam('category', implode(',', $category));
        }

        if (null !== $core = $query->getCore()) {
            $request->addParam('core', implode(',', $core));
        }

        if (null !== $collection = $query->getCollection()) {
            $request->addParam('collection', implode(',', $collection));
        }

        if (null !== $shard = $query->getShard()) {
            $request->addParam('shard', implode(',', $shard));
        }

        if (null !== $replicaType = $query->getReplicaType()) {
            $request->addParam('replica_type', implode(',', $replicaType));
        }

        return $request;
    }
}
