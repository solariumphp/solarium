<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink
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

/**
 * Class ShardState
 */
class ShardState extends AbstractState
{
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $range;
    /** @var  ReplicaState[] */
    protected $replicas;
    /** @var string Id of the shard leader */
    protected $shardLeader;
    /** @var  string[] An array of all ids of the active replicas */
    protected $activeReplicas;
    /** @var  string Shard is active or inactive */
    protected $state;

    /** @var string The normal/default state of a shard. */
    const ACTIVE = 'active';
    /**
     * @var string A shard is put in that state after it has been successfully split.
     */
    const INACTIVE = 'inactive';
    /**
     * @var string When a shard is split, the new sub-shards are put in that state while the split operation is in progress.
     */
    const CONSTRUCTION = 'construction';
    /**
     * @var string Sub-shards of a split shard are put in that state, when they need to create replicas in order to meet the collection's replication factor.
     */
    const RECOVERY = 'recovery';
    /**
     * @var string Sub-shards of a split shard are put in that state when the split is deemed failed by the overseer even though all replicas are active because either the leader node is no longer live or has a different ephemeral owner (zk session id).
    */
    const RECOVERY_FAILED = 'recovery_failed';

    /**
     * Returns the name of the shard
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the range of the shard
     *
     * @return string
     */
    public function getRange(): string
    {
        return $this->range;
    }

    /**
     * Returns if the shard is active or inactive
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Returns ReplicaState instances of all replicas
     *
     * @return ReplicaState[]
     */
    public function getReplicas(): array
    {
        return $this->replicas;
    }

    /**
     * Returns a ReplicaState instance of the shard leader or null if no shard leader is active
     *
     * @return ReplicaState|null
     */
    public function getShardLeader()//: ?ReplicaState // TODO can return null, wait for PHP 7.1 for this to work
    {
        if (isset($this->replicas[$this->shardLeader]) && $this->replicas[$this->shardLeader]->isActive()) {
            return $this->replicas[$this->shardLeader];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getShardLeaderBaseUri()//: ?string // TODO wait for PHP 7.1 to support this
    : string
    {
        if ($this->getShardLeader() instanceof ReplicaState) {
            return $this->getShardLeader() !== null ? $this->getShardLeader()->getServerUri() : null;
        }

        return null;
    }

    /**
     * Array with node names as keys and base URIs as values.
     *
     * @return string[]
     */
    public function getNodesBaseUris(): array
    {
        $uris = array();
        foreach ($this->getReplicas() as $replica) {
            if ($replica->getState() === ReplicaState::ACTIVE) {
                $uris[$replica->getNodeName()] = $replica->getServerUri();
            }
        }

        return $uris;
    }

    /**
     * Return an array of all active replicas
     *
     * @return ReplicaState[]
     */
    public function getActiveReplicas(): array
    {
        $replicas = array();
        foreach ($this->activeReplicas as $replica) {
            $replicas[] = $replica;
        }

        return $replicas;
    }

    protected function init()
    {
        $this->name = key($this->stateRaw);
        $this->stateRaw = reset($this->stateRaw);
        $this->range = $this->getStateProp(ClusterState::RANGE_PROP);
        $this->state = $this->getStateProp(ClusterState::STATE_PROP);

        $replicas = $this->getStateProp(ClusterState::REPLICAS_PROP);
        // Reset replicas property
        $this->replicas = array();

        foreach ($replicas as $replicaName => $replica) {
            $this->replicas[$replicaName] = new ReplicaState(array($replicaName => $replica), $this->liveNodes);
            if ($this->replicas[$replicaName]->isLeader()) {
                $this->shardLeader = $replicaName;
            }
        }
    }
}
