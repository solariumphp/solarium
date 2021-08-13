<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\ClusterStatusResult;
use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

/**
 * Class ClusterStatus.
 *
 * @see https://solr.apache.org/guide/cluster-node-management.html#clusterstatus
 */
class ClusterStatus extends AbstractAsyncAction
{
    /**
     * Returns the action type of the Collections API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CollectionsQuery::ACTION_CLUSTERSTATUS;
    }

    /**
     * The collection name for which information is requested. If omitted, information on all collections in the
     * cluster will be returned.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection(string $collection): self
    {
        $this->setOption('collection', $collection);

        return $this;
    }

    /**
     * Get collection name.
     *
     * @return string
     */
    public function getCollection(): string
    {
        return $this->getOption('collection');
    }

    /**
     * The shard(s) for which information is requested. Multiple shard names can be specified as a comma-separated list.
     *
     * @param string $shard
     *
     * @return self Provides fluent interface
     */
    public function setShard(string $shard): self
    {
        $this->setOption('shard', $shard);

        return $this;
    }

    /**
     * Get shard.
     *
     * @return string|null
     */
    public function getShard(): ?string
    {
        return $this->getOption('shard');
    }

    /**
     * This can be used if you need the details of the shard where a particular document belongs to and you don’t know
     * which shard it falls under.
     *
     * @param string $route
     *
     * @return self Provides fluent interface
     */
    public function setRoute(string $route): self
    {
        $this->setOption('_route_', $route);

        return $this;
    }

    /**
     * Get route.
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->getOption('route');
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return ClusterStatusResult::class;
    }
}
