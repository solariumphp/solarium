<?php

namespace Solarium\Core\Client\State;

/**
 * Interface StateInterface.
 */
interface StateInterface
{
    /**
     * @param array $state     State array received from Zookeeper or Solr
     * @param array $liveNodes
     *
     * @return mixed
     */
    public function update(array $state, array $liveNodes);

    /**
     * @param string $name
     * @param null   $defaultValue
     *
     * @return mixed
     */
    public function getStateProp(string $name, $defaultValue = null);
}
