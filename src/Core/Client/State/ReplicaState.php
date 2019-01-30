<?php

namespace Solarium\Core\Client\State;

/**
 * Class ReplicaState.
 */
class ReplicaState extends AbstractState
{
    /** @var string Name of the replica */
    protected $name = '';

    /** @var string Name of the core */
    protected $core = '';

    /** @var string Base uri of shard replica */
    protected $baseUri = '';

    /** @var string */
    protected $nodeName = '';

    /** @var bool Whether or not this replica is a shard leader */
    protected $leader = false;

    /** @var string Replica state, one of the following: active, down, recovering or recovery_failed */
    protected $state;

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

    protected function init()
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
