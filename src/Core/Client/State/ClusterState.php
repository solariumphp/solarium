<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2018 Jeroen Steggink
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Solarium\Core\Client\State;

use Solarium\Exception\RuntimeException;

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
     * @param array $clusterStatus
     */
    public function __construct(array $clusterStatus)
    {
        $this->clusterStatus = $clusterStatus;

        $this->aliases = $clusterStatus[self::ALIASES_PROP] ?? [];
        if(isset($clusterStatus[self::COLLECTIONS_NODE])) {
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
     * @return CollectionState
     * @throws RuntimeException
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