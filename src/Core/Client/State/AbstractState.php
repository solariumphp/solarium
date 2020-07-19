<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\State;

/**
 * Class State.
 */
abstract class AbstractState implements StateInterface
{
    /**
     * @var array List of live nodes
     */
    protected $liveNodes;

    /**
     * @var array State array retrieved by ZkStateReader
     */
    protected $stateRaw;

    /**
     * State constructor.
     *
     * @param array $collections State array received from Zookeeper
     * @param array $liveNodes
     */
    public function __construct(array $collections, array $liveNodes)
    {
        $this->update($collections, $liveNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $state, array $liveNodes): void
    {
        $this->stateRaw = $state;
        $this->liveNodes = $liveNodes;
        $this->init();
    }

    /**
     * @param string     $name
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getStateProp(string $name, $defaultValue = null)
    {
        return $this->stateRaw[$name] ?? $defaultValue;
    }

    /**
     * Initialize method.
     */
    abstract protected function init();
}
