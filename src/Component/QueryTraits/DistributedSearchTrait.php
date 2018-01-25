<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait DistributedSearchTrait
{
    /**
     * Get a DistributedSearch component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\DistributedSearch
     */
    public function getDistributedSearch()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DISTRIBUTEDSEARCH, true);
    }
}
