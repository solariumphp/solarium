<?php

namespace Solarium\Core\Client\State;

use Solarium\Exception\RuntimeException;

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
        return $this->getState()[ClusterState::AUTO_ADD_REPLICAS] ?? false;
    }

    public function isAutoCreated(): bool
    {
        return $this->getState()[ClusterState::AUTO_CREATED] ?? false;
    }

    /**
     * Returns collection aliases.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->getState()[ClusterState::ALIASES_PROP] ?? [];
    }

    /**
<<<<<<< HEAD:src/QueryType/Server/Collections/Result/CollectionState.php
     * Returns cluster properties.
     *
     * @return string[]
=======
     * Returns the config name of the collection.
     * @return string
>>>>>>> jsteggink-collections-api:src/Core/Client/State/CollectionState.php
     */
    public function getConfigName(): string
    {
        return $this->getState()[ClusterState::CONFIG_NAME_PROP] ?? '';
    }

    /**
     * @return int
     */
    public function getMaxShardsPerNode(): int
    {
        return $this->getState()[ClusterState::MAX_SHARDS_PER_NODE];
    }

    /**
     * @return int
     */
    public function getReplicationFactor(): int
    {
        return $this->getState()[ClusterState::REPLICATION_FACTOR];
    }

    /**
     * @return string
     */
    public function getRouterName(): string
    {
        return $this->getState()[ClusterState::ROUTER_PROP]['name'];
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
<<<<<<< HEAD:src/QueryType/Server/Collections/Result/CollectionState.php
            if (ShardState::ACTIVE == $shard->getState()  && !empty($shard->getShardLeaderBaseUri())) {
=======
            if ($shard->getState() === ShardState::ACTIVE && $shard->getShardLeaderBaseUri() !== null) {
>>>>>>> jsteggink-collections-api:src/Core/Client/State/CollectionState.php
                $uris[$shardName] = $shard->getShardLeaderBaseUri();
            }
        }

        return $uris;
    }

    /**
     * Array with node names as keys and base URIs as values.
     *
     * @return string[]
<<<<<<< HEAD:src/QueryType/Server/Collections/Result/CollectionState.php
     *
     * @throws SolrCloudException
=======
     * @throws RuntimeException
>>>>>>> jsteggink-collections-api:src/Core/Client/State/CollectionState.php
     */
    public function getNodesBaseUris(): array
    {
        $uris = array();

        foreach ($this->getShards() as $shard) {
<<<<<<< HEAD:src/QueryType/Server/Collections/Result/CollectionState.php
            if (ShardState::ACTIVE == $shard->getState()) {
=======
            if ($shard->getState() === ShardState::ACTIVE) {
>>>>>>> jsteggink-collections-api:src/Core/Client/State/CollectionState.php
                $uris = array_merge($shard->getNodesBaseUris(), $uris);
            }
        }

        if (empty($uris)) {
            throw new RuntimeException('No Solr nodes are available for this collection.');
        }

        return $uris;
    }

    public function getTlogReplicas(): string
    {
        return $this->getState()[ClusterState::TLOG_REPLICAS];
    }

    public function getZnodeVersion(): string
    {
        return $this->getState()[ClusterState::ZNODE_VERSION];
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
<<<<<<< HEAD:src/QueryType/Server/Collections/Result/CollectionState.php
        $this->shards = [];
        foreach ($this->getState()[ZkStateReader::SHARDS_PROP] as $shardName => $shardState) {
            // @todo liveNodes?
            $this->shards[$shardName] = new ShardState([$shardName => $shardState], $this->liveNodes);
=======
        $this->shards = array();
        foreach ($this->getState()[ClusterState::SHARDS_PROP] as $shardName => $shardState) {
            $this->shards[$shardName] = new ShardState(array($shardName => $shardState), $this->liveNodes);
>>>>>>> jsteggink-collections-api:src/Core/Client/State/CollectionState.php
        }
    }

    protected function init()
    {
        $this->name = key($this->stateRaw);
        $this->setShards();
    }
}
