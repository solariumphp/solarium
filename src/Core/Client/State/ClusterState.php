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
 * Cluster State.
 */
class ClusterState
{
    const ALIASES_PROP = 'aliases';

    const AUTO_CREATED = 'autoCreated';

    const AUTO_ADD_REPLICAS = 'autoAddReplicas';

    const BASE_URL_PROP = 'base_url';

    const COLLECTION_PROP = 'collection';

    const COLLECTIONS_NODE = 'collections';

    const CONFIG_NAME_PROP = 'configName';

    const CORE_NAME_PROP = 'core';

    const LEADER_PROP = 'leader';

    const LIVE_NODES_NODE = 'live_nodes';

    const MAX_CORES_PER_NODE = 'maxCoresPerNode';

    const MAX_SHARDS_PER_NODE = 'maxShardsPerNode';

    const NODE_NAME_PROP = 'node_name';

    const NRT_REPLICAS = 'nrtReplicas';

    const PULL_REPLICAS = 'pullReplicas';

    const RANGE_PROP = 'range';

    const REPLICAS_PROP = 'replicas';

    const REPLICATION_FACTOR = 'replicationFactor';

    const ROLES_PROP = 'roles';

    const ROUTER_PROP = 'router';

    const SHARDS_PROP = 'shards';

    const STATE_PROP = 'state';

    const TLOG_REPLICAS = 'tlogReplicas';

    const ZNODE_VERSION = 'znodeVersion';

    /**
     * @var array CLUSTERSTATUS array of parsed json
     */
    protected $clusterStatus;

    /*
     * @var string[]
     */
    protected $aliases;

    /**
     * @var CollectionState[]
     */
    protected $collections;

    /**
     * @var string[]
     */
    protected $liveNodes;

    /**
     * @var string[]
     */
    protected $roles;

    /**
     * ClusterState constructor.
     *
     * @param array $clusterStatus
     */
    public function __construct(array $clusterStatus)
    {
        $this->clusterStatus = $clusterStatus;

        $this->aliases = $clusterStatus[self::ALIASES_PROP] ?? [];
        $this->collections = [];
        if (isset($clusterStatus[self::COLLECTIONS_NODE])) {
            foreach ($clusterStatus[self::COLLECTIONS_NODE] as $collectionName => $collectionState) {
                $this->collections[$collectionName] = new CollectionState(
                    [$collectionName => $collectionState],
                    $clusterStatus[self::LIVE_NODES_NODE]
                );
            }
        }

        $this->liveNodes = $clusterStatus[self::LIVE_NODES_NODE] ?? [];
        $this->roles = $clusterStatus[self::ROLES_PROP] ?? [];
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return ClusterState[]
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * @param string $collectionName
     *
     * @throws RuntimeException
     *
     * @return CollectionState
     */
    public function getCollectionState(string $collectionName): CollectionState
    {
        if ($this->collectionExists($collectionName)) {
            return $this->collections[$collectionName];
        }
        throw new RuntimeException(sprintf("Collection '%s' does not exist.", $collectionName));
    }

    /**
     * Check if collection exists in SolrCloud.
     *
     * @param string $collectionName
     *
     * @return bool
     */
    public function collectionExists(string $collectionName): bool
    {
        return isset($this->collections[$collectionName]);
    }

    /**
     * @return string[]
     */
    public function getLiveNodes(): array
    {
        return $this->liveNodes;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
