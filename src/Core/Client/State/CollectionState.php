<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\State;

use Solarium\Exception\RuntimeException;

/**
 * Class for describing a SolrCloud collection endpoint.
 */
class CollectionState extends AbstractState
{
    /**
     * @var string Name of the collection
     */
    protected $name;

    /**
     * @var ShardState[]
     */
    protected $shards;

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
     * Name of the collection.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAutoAddReplicas(): bool
    {
        return $this->getState()[ClusterState::AUTO_ADD_REPLICAS] ?? false;
    }

    /**
     * @return bool
     */
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
     * Returns the config name of the collection.
     *
     * @return string
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
            if (ShardState::ACTIVE === $shard->getState() && null !== $shard->getShardLeaderBaseUri()) {
                $uris[$shardName] = $shard->getShardLeaderBaseUri();
            }
        }

        return $uris;
    }

    /**
     * Array with node names as keys and base URIs as values.
     *
     * @throws RuntimeException
     *
     * @return string[]
     */
    public function getNodesBaseUris(): array
    {
        $uris = [];

        foreach ($this->getShards() as $shard) {
            if (ShardState::ACTIVE === $shard->getState()) {
                $uris += $shard->getNodesBaseUris();
            }
        }

        if (empty($uris)) {
            throw new RuntimeException('No Solr nodes are available for this collection.');
        }

        return $uris;
    }

    /**
     * @return string
     */
    public function getTlogReplicas(): string
    {
        return $this->getState()[ClusterState::TLOG_REPLICAS];
    }

    /**
     * @return string
     */
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

    /**
     * Clear and set shards.
     */
    protected function setShards()
    {
        // Clear shards first
        $this->shards = [];

        foreach ($this->getState()[ClusterState::SHARDS_PROP] as $shardName => $shardState) {
            $this->shards[$shardName] = new ShardState([$shardName => $shardState], $this->liveNodes);
        }
    }

    /**
     * init.
     */
    protected function init()
    {
        $this->name = key($this->stateRaw);
        $this->setShards();
    }
}
