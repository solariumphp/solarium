<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait ReRankQueryTrait
{
    /**
     * Get a ReRankQuery component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\ReRankQuery
     */
    public function getReRankQuery()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_RERANKQUERY, true);
    }
}
