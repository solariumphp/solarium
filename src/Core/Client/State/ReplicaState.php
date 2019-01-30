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
 * Class ReplicaState
 */
class ReplicaState extends AbstractState
{
    /** @var  string Name of the replica */
    protected $name = '';
    /** @var  string Name of the core */
    protected $core = '';
    /** @var  string Base uri of shard replica */
    protected $baseUri = '';
    /** @var  string */
    protected $nodeName = '';
    /** @var  bool Whether or not this replica is a shard leader */
    protected $leader = false;
    /** @var  string Replica state, one of the following: active, down, recovering or recovery_failed */
    protected $state;

    /**
     * @var string The replica is ready to receive updates and queries.
     */
    const ACTIVE = 'active';
    /**
     * @var string The first state before recovering.
     */
    const DOWN = 'down';
    /**
     * @var string The node is recovering from the leader.
     */
    const RECOVERING = 'recovering';
    /**
     * @var string Recovery attempts have not worked, something is not right.
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
     * Returns if the replica is active or inactive
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
        return $this->state === self::ACTIVE;
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
