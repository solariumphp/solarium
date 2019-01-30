<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\CreateResult;

/**
 * Class Create.
 *
 * @see https://lucene.apache.org/solr/guide/collections-api.html#create
 */
class Create extends AbstractAction
{
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
     * The name of the collection to be created. This parameter is required.
     *
     * @param string $collection
     */
    public function setName(string $collection)
    {
        $this->setOption('name', $collection);
    }

    /**
     * Get the name of the collection to be created.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getOption('name');
    }

    /**
     * The router name that will be used. The router defines how documents will be distributed among the shards.
     * Possible values are implicit or compositeId, which is the default.
     *
     * @param string $routerName
     *
     * @return self $this
     */
    public function setRouterName(string $routerName)
    {
        $this->setOption('router.name', $routerName);

        return $this;
    }

    /**
     * Returns the router name.
     *
     * @return mixed
     */
    public function getRouterName()
    {
        return $this->getOption('router.name');
    }

    /**
     * The number of shards to be created as part of the collection.
     * This is a required parameter when the router.name is compositeId.
     *
     * @param int $numShards
     *
     * @return self $this
     */
    public function setNumShards(int $numShards)
    {
        $this->setOption('numShards', $numShards);

        return $this;
    }

    /**
     * Returns the number of shards.
     *
     * @return mixed
     */
    public function getNumShards()
    {
        return $this->getOption('numShards');
    }

    /**
     * A comma separated list of shard names, e.g., shard-x,shard-y,shard-z.
     * This is a required parameter when the router.name is implicit.
     *
     * @param string $shards
     *
     * @return self $this
     */
    public function setShards(string $shards)
    {
        $this->setOption('shards', $shards);

        return $this;
    }

    /**
     * Returns the shards.
     *
     * @return mixed
     */
    public function getShards()
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
    public function setReplicationFactor(int $replicationFactor)
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
    public function setNrtReplicas(int $nrtReplicas)
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
    public function setTlogReplicas(int $tlogReplicas)
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
    public function setPullReplicas(int $pullReplicas)
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
    public function setMaxShardsPerNode(int $maxShardsPerNode)
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
    public function setCreateNodeSet(string $createNodeSet)
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
    public function setCreateNodeSetShuffle(bool $shuffle)
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
    public function setCollectionConfigName(string $configName)
    {
        $this->setOption('collection.configName', $configName);

        return $this;
    }

    /**
     * Get the collection config name.
     *
     * @return string
     */
    public function getCollectionConfigName(): string
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
    public function setRouterField(string $routerField)
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
    public function setProperty(string $name, string $value)
    {
        $option = 'property.'.$name;
        $this->setOption($option, $value);

        return $this;
    }

    /**
     * Get a previously added property.
     *
     * @return string
     */
    public function getProperty($name): string
    {
        $option = 'property.'.$name;

        return (string) $this->getOption($option);
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
    public function setAutoAddReplicas(bool $autoAddReplicas)
    {
        $this->setOption('autoAddReplicas', $autoAddReplicas);

        return $this;
    }

    /**
     * Request ID to track this action which will be processed asynchronously.
     *
     * @param string $id
     *
     * @return self Provides fluent interface
     */
    public function setAsync(string $id)
    {
        $this->setOption('async', $id);

        return $this;
    }

    /**
     * Replica placement rules.
     *
     * @param string $rule
     *
     * @return self Provides fluent interface
     */
    public function setRule(string $rule)
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
    public function setSnitch(string $snitch)
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
    public function setPolicy(string $policy)
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
    public function setWaitForFinalState(bool $waitForFinalState)
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
    public function setWithCollection(string $withCollection)
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
