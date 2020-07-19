<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\State;

/**
 * Class ShardState.
 */
class ShardState extends AbstractState
{
    /** The normal/default state of a shard. */
    const ACTIVE = 'active';

    /**
     * A shard is put in that state after it has been successfully split.
     */
    const INACTIVE = 'inactive';

    /**
     * When a shard is split, the new sub-shards are put in that state while the split operation is in progress.
     */
    const CONSTRUCTION = 'construction';

    /**
     * Sub-shards of a split shard are put in that state, when they need to create replicas in order to meet the
     * collection's replication factor.
     */
    const RECOVERY = 'recovery';

    /**
     * Sub-shards of a split shard are put in that state when the split is deemed failed by the overseer even though all
     * replicas are active because either the leader node is no longer live or has a different ephemeral owner (zk
     * session id).
     */
    const RECOVERY_FAILED = 'recovery_failed';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $range;

    /**
     * @var ReplicaState[]
     */
    protected $replicas;

    /**
     * @var string Id of the shard leader
     */
    protected $shardLeader;

    /**
     * @var string[] An array of all ids of the active replicas
     */
    protected $activeReplicas;

    /**
     * @var string Shard is active or inactive
     */
    protected $state;

    /**
     * Returns the name of the shard.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the range of the shard.
     *
     * @return string
     */
    public function getRange(): string
    {
        return $this->range;
    }

    /**
     * Returns if the shard is active or inactive.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Returns ReplicaState instances of all replicas.
     *
     * @return ReplicaState[]
     */
    public function getReplicas(): array
    {
        return $this->replicas;
    }

    /**
     * Returns a ReplicaState instance of the shard leader or null if no shard leader is active.
     *
     * @return ReplicaState|null
     */
    public function getShardLeader(): ?ReplicaState
    {
        if (isset($this->replicas[$this->shardLeader]) && $this->replicas[$this->shardLeader]->isActive()) {
            return $this->replicas[$this->shardLeader];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getShardLeaderBaseUri(): ?string
    {
        if ($this->getShardLeader() instanceof ReplicaState) {
            return null !== $this->getShardLeader() ? $this->getShardLeader()->getServerUri() : null;
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
        $uris = [];
        foreach ($this->getReplicas() as $replica) {
            if (!isset($uris[$replica->getNodeName()]) && ReplicaState::ACTIVE === $replica->getState()) {
                $uris[$replica->getNodeName()] = $replica->getServerUri();
            }
        }

        return $uris;
    }

    /**
     * Return an array of all active replicas.
     *
     * @return ReplicaState[]
     */
    public function getActiveReplicas(): array
    {
        $replicas = [];
        foreach ($this->activeReplicas as $replica) {
            $replicas[] = $replica;
        }

        return $replicas;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->name = key($this->stateRaw);
        $this->stateRaw = reset($this->stateRaw);
        $this->range = $this->getStateProp(ClusterState::RANGE_PROP);
        $this->state = $this->getStateProp(ClusterState::STATE_PROP);

        $replicas = $this->getStateProp(ClusterState::REPLICAS_PROP);
        // Reset replicas property
        $this->replicas = [];

        foreach ($replicas as $replicaName => $replica) {
            // @todo liveNodes?
            $this->replicas[$replicaName] = new ReplicaState([$replicaName => $replica], $this->liveNodes);
            if ($this->replicas[$replicaName]->isLeader()) {
                $this->shardLeader = $replicaName;
            }
        }
    }
}
