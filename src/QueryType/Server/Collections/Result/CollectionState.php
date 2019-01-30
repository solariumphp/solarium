<?php

namespace Solarium\QueryType\Server\Collections\Result;

use Solarium\Exception\SolrCloudException;

/**
 * Class for describing a SolrCloud collection endpoint.
 */
class CollectionState extends AbstractState
{
    /** @var string Name of the collection */
    protected $name;

    /** @var ShardState[] */
    protected $shards;

    /**
     * @param array $collection
     * @param array $liveNodes
     */
    public function __construct(array $collection, array $liveNodes)
    {
        parent::__construct($collection, $liveNodes);
    }

    /**
     * Name of the collection.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     *
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__.'::__toString'."\n".print_r($this->stateRaw, true);
    }

    /**
     * @return bool
     */
    public function isAutoAddReplicas(): bool
    {
        return $this->getState()[ZkStateReader::AUTO_ADD_REPLICAS];
    }

    /**
     * Returns collection aliases.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return isset($this->getState()[ZkStateReader::ALIASES_PROP]) ? $this->getState()[ZkStateReader::ALIASES_PROP] : [];
    }

    /**
     * Returns cluster properties.
     *
     * @return string[]
     */
    /* TODO probably doesn't exist anymore
     * public function getClusterProperties(): array
    {
        return $this->getState()[ZkStateReader::CLUSTER_PROP];
    }*/

    /**
     * @return int
     */
    public function getMaxShardsPerNode(): int
    {
        return $this->getState()[ZkStateReader::MAX_SHARDS_PER_NODE];
    }

    /**
     * @return int
     */
    public function getReplicationFactor(): int
    {
        return $this->getState()[ZkStateReader::REPLICATION_FACTOR];
    }

    /**
     * @return string
     */
    public function getRouterName(): string
    {
        return $this->getState()[ZkStateReader::ROUTER_PROP]['name'];
    }

    /**
     * Return all shards.
     *
     * @return ShardState[]
     */
    public function getShards(): array
    {
        return $this->shards;
    }

    /**
     * @return ReplicaState[]
     */
    public function getShardLeaders(): array
    {
        $leaders = [];

        foreach ($this->shards as $shardName => $shard) {
            if ($shard->getShardLeader() instanceof ReplicaState) {
                $leaders[$shardName] = $shard->getShardLeader();
            }
        }

        return $leaders;
    }

    /**
     * Array with shard names as keys and base URIs as values.
     *
     * @return string[]
     */
    public function getShardLeadersBaseUris(): array
    {
        $uris = [];

        foreach ($this->getShards() as $shardName => $shard) {
            if (ShardState::ACTIVE == $shard->getState()  && !empty($shard->getShardLeaderBaseUri())) {
                $uris[$shardName] = $shard->getShardLeaderBaseUri();
            }
        }

        return $uris;
    }

    /**
     * Array with node names as keys and base URIs as values.
     *
     * @return string[]
     *
     * @throws SolrCloudException
     */
    public function getNodesBaseUris(): array
    {
        $uris = array();

        foreach ($this->getShards() as $shard) {
            if (ShardState::ACTIVE == $shard->getState()) {
                $uris = array_merge($shard->getNodesBaseUris(), $uris);
            }
        }

        if (empty($uris)) {
            throw new SolrCloudException('No Solr nodes are available for this collection.');
        }

        return $uris;
    }

    /**
     * Get state array without the collection name key.
     *
     * @return mixed
     */
    protected function getState()
    {
        return $this->stateRaw[$this->name];
    }

    protected function setShards()
    {
        // Clear shards first
        $this->shards = [];
        foreach ($this->getState()[ZkStateReader::SHARDS_PROP] as $shardName => $shardState) {
            // @todo liveNodes?
            $this->shards[$shardName] = new ShardState([$shardName => $shardState], $this->liveNodes);
        }
    }

    protected function init()
    {
        $this->name = key($this->stateRaw);
        $this->setShards();
    }
}
