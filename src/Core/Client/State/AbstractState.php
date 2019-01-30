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
 * Class State
 */
abstract class AbstractState implements StateInterface
{
    /** @var array List of live nodes */
    protected $liveNodes;
    /** @var  array State array retrieved by ZkStateReader */
    protected $stateRaw;

    /**
     * State constructor.
     * @param array $collections State array received from Zookeeper
     * @param array $liveNodes
     */
    public function __construct(array $collections, array $liveNodes)
    {
        $this->update($collections, $liveNodes);
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $state, array $liveNodes)
    {
        $this->stateRaw = $state;
        $this->liveNodes = $liveNodes;
        $this->init();
    }

    /**
     * @param string     $name
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getStateProp(string $name, $defaultValue = null)
    {
        if (isset($this->stateRaw[$name])) {
            return $this->stateRaw[$name];
        }

        return $defaultValue;
    }

    /**
     * Initialize method
     */
    abstract protected function init();
}
