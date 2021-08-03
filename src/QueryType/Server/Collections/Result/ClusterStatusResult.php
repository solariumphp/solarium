<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Result;

use Solarium\Core\Client\State\ClusterState;
use Solarium\QueryType\Server\Query\AbstractResult;

/**
 * ClusterStatusResult.
 */
class ClusterStatusResult extends AbstractResult
{
    /**
     * Returns the cluster state.
     *
     * @return ClusterState
     */
    public function getClusterState(): ClusterState
    {
        $this->parseResponse();
        if (isset($this->getData()['cluster'])) {
            return new ClusterState($this->getData()['cluster']);
        }

        return new ClusterState([]);
    }
}
