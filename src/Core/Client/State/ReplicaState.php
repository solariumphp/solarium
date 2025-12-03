<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\State;

/**
 * Class ReplicaState.
 */
class ReplicaState extends AbstractState
{
    /**
     * The replica is ready to receive updates and queries.
     */
    const ACTIVE = 'active';

    /**
     * The first state before recovering.
     */
    const DOWN = 'down';

    /**
     * The node is recovering from the leader.
     */
    const RECOVERING = 'recovering';

    /**
     * Recovery attempts have not worked, something is not right.
     */
    const RECOVERY_FAILED = 'recovery_failed';

    /**
     * Name of the replica.
     */
    protected string $name = '';

    /**
     * Name of the core.
     */
    protected string $core = '';

    /**
     * Base URI of shard replica.
     */
    protected string $baseUri = '';

    /**
     * Name of the node.
     */
    protected string $nodeName = '';

    /**
     * Whether or not this replica is a shard leader.
     */
    protected bool $leader = false;

    /**
     * Replica state, one of the following: active, down, recovering or recovery_failed.
     */
    protected string $state;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns if the replica is active or inactive.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getCore(): string
    {
        return $this->core;
    }

    /**
     * @return string
     */
    public function getServerUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return $this->nodeName;
    }

    /**
     * @return bool
     */
    public function isLeader(): bool
    {
        return $this->leader;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return self::ACTIVE === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        $this->name = key($this->stateRaw);
        $this->stateRaw = reset($this->stateRaw);

        $this->core = $this->getStateProp(ClusterState::CORE_NAME_PROP, '');
        $this->baseUri = $this->getStateProp(ClusterState::BASE_URL_PROP, '');
        $this->nodeName = $this->getStateProp(ClusterState::NODE_NAME_PROP, '');
        $this->leader = $this->getStateProp(ClusterState::LEADER_PROP, false);

        if (\in_array($this->nodeName, $this->liveNodes, true)) {
            $this->state = $this->getStateProp(ClusterState::STATE_PROP);
        } else {
            $this->state = self::DOWN;
        }
    }
}
