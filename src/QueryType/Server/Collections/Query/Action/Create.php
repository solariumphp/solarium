<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\CreateResult;
use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;
use Solarium\QueryType\Server\Query\Action\NameParameterTrait;

/**
 * Class Create.
 *
 * @see https://solr.apache.org/guide/collection-management.html#create
 */
class Create extends AbstractAsyncAction
{
    use NameParameterTrait;

    /**
     * Returns the action type of the Collections API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CollectionsQuery::ACTION_CREATE;
    }

    /**
     * The router name that will be used. The router defines how documents will be distributed among the shards.
     * Possible values are implicit or compositeId, which is the default.
     *
     * @param string $routerName
     *
     * @return self Provides fluent interface
     */
    public function setRouterName(string $routerName): self
    {
        $this->setOption('router.name', $routerName);

        return $this;
    }

    /**
     * Returns the router name.
     *
     * @return string|null
     */
    public function getRouterName(): ?string
    {
        return $this->getOption('router.name');
    }

    /**
     * The number of shards to be created as part of the collection.
     * This is a required parameter when the router.name is compositeId.
     *
     * @param int $numShards
     *
     * @return self Provides fluent interface
     */
    public function setNumShards(int $numShards): self
    {
        $this->setOption('numShards', $numShards);

        return $this;
    }

    /**
     * Returns the number of shards.
     *
     * @return int|null
     */
    public function getNumShards(): ?int
    {
        return $this->getOption('numShards');
    }

    /**
     * A comma separated list of shard names, e.g., shard-x,shard-y,shard-z.
     * This is a required parameter when the router.name is implicit.
     *
     * @param string $shards
     *
     * @return self Provides fluent interface
     */
    public function setShards(string $shards): self
    {
        $this->setOption('shards', $shards);

        return $this;
    }

    /**
     * Returns the shards.
     *
     * @return string|null
     */
    public function getShards(): ?string
    {
        return $this->getOption('shards');
    }

    /**
     * The number of replicas to be created for each shard. The default is 1.
     *
     * @param int $replicationFactor
     *
     * @return self Provides fluent interface
     */
    public function setReplicationFactor(int $replicationFactor): self
    {
        $this->setOption('replicationFactor', $replicationFactor);

        return $this;
    }

    /**
     * The number of NRT (Near-Real-Time) replicas to create for this collection.
     *
     * @param int $nrtReplicas
     *
     * @return self Provides fluent interface
     */
    public function setNrtReplicas(int $nrtReplicas): self
    {
        $this->setOption('nrtReplicas', $nrtReplicas);

        return $this;
    }

    /**
     * The number of TLOG replicas to create for this collection.
     *
     * @param int $tlogReplicas
     *
     * @return self Provides fluent interface
     */
    public function setTlogReplicas(int $tlogReplicas): self
    {
        $this->setOption('tlogReplicas', $tlogReplicas);

        return $this;
    }

    /**
     * The number of PULL replicas to create for this collection.
     *
     * @param int $pullReplicas
     *
     * @return self Provides fluent interface
     */
    public function setPullReplicas(int $pullReplicas): self
    {
        $this->setOption('pullReplicas', $pullReplicas);

        return $this;
    }

    /**
     * When creating collections, the shards and/or replicas are spread across all available (i.e., live) nodes, and
     * two replicas of the same shard will never be on the same node.
     *
     * @param int $maxShardsPerNode
     *
     * @return self Provides fluent interface
     */
    public function setMaxShardsPerNode(int $maxShardsPerNode): self
    {
        $this->setOption('maxShardsPerNode', $maxShardsPerNode);

        return $this;
    }

    /**
     * Allows defining the nodes to spread the new collection across. The format is a comma-separated list of
     * node_names, such as localhost:8983_solr,localhost:8984_solr,localhost:8985_solr.
     *
     * @param string $createNodeSet
     *
     * @return self Provides fluent interface
     */
    public function setCreateNodeSet(string $createNodeSet): self
    {
        $this->setOption('createNodeSet', $createNodeSet);

        return $this;
    }

    /**
     * Controls wether or not the shard-replicas created for this collection will be assigned to the nodes specified by
     * the createNodeSet in a sequential manner, or if the list of nodes should be shuffled prior to creating
     * individual replicas.
     *
     * @param bool $shuffle
     *
     * @return self Provides fluent interface
     */
    public function setCreateNodeSetShuffle(bool $shuffle): self
    {
        $this->setOption('createNodeSet.shuffle', $shuffle);

        return $this;
    }

    /**
     * Set the collection config name.
     *
     * @param string $configName
     *
     * @return self Provides fluent interface
     */
    public function setCollectionConfigName(string $configName): self
    {
        $this->setOption('collection.configName', $configName);

        return $this;
    }

    /**
     * Get the collection config name.
     *
     * @return string|null
     */
    public function getCollectionConfigName(): ?string
    {
        return $this->getOption('collection.configName');
    }

    /**
     * If this parameter is specified, the router will look at the value of the field in an input document to compute
     * the hash and identify a shard instead of looking at the uniqueKey field. If the field specified is null in the
     * document, the document will be rejected.
     *
     * @param string $routerField
     *
     * @return self Provides fluent interface
     */
    public function setRouterField(string $routerField): self
    {
        $this->setOption('router.field', $routerField);

        return $this;
    }

    /**
     * Set core property name to value.
     *
     * @param string $name
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setProperty(string $name, string $value): self
    {
        $this->setOption('property.'.$name, $value);

        return $this;
    }

    /**
     * Get a previously added property.
     *
     * @param string $name property name
     *
     * @return string|null
     */
    public function getProperty(string $name): ?string
    {
        return $this->getOption('property.'.$name);
    }

    /**
     * When set to true, enables automatic addition of replicas when the number of active replicas falls below the
     * value set for replicationFactor. This may occur if a replica goes down, for example.
     * The default is false, which means new replicas will not be added.
     *
     * @param bool $autoAddReplicas
     *
     * @return self Provides fluent interface
     */
    public function setAutoAddReplicas(bool $autoAddReplicas): self
    {
        $this->setOption('autoAddReplicas', $autoAddReplicas);

        return $this;
    }

    /**
     * Replica placement rules.
     *
     * @param string $rule
     *
     * @return self Provides fluent interface
     */
    public function setRule(string $rule): self
    {
        $this->setOption('rule', $rule);

        return $this;
    }

    /**
     * Details of the snitch provider.
     *
     * @param string $snitch
     *
     * @return self Provides fluent interface
     */
    public function setSnitch(string $snitch): self
    {
        $this->setOption('snitch', $snitch);

        return $this;
    }

    /**
     * Name of the collection-level policy.
     *
     * @param string $policy
     *
     * @return self Provides fluent interface
     */
    public function setPolicy(string $policy): self
    {
        $this->setOption('policy', $policy);

        return $this;
    }

    /**
     * If true, the request will complete only when all affected replicas become active. The default is false, which
     * means that the API will return the status of the single action, which may be before the new replica is online
     * and active.
     *
     * @param bool $waitForFinalState
     *
     * @return self Provides fluent interface
     */
    public function setWaitForFinalState(bool $waitForFinalState): self
    {
        $this->setOption('waitForFinalState', $waitForFinalState);

        return $this;
    }

    /**
     * The name of the collection with which all replicas of this collection must be co-located.
     * The collection must already exist and must have a single shard named shard1.
     *
     * @param string $withCollection
     *
     * @return self Provides fluent interface
     */
    public function setWithCollection(string $withCollection): self
    {
        $this->setOption('withCollection', $withCollection);

        return $this;
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return CreateResult::class;
    }
}
