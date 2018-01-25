<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait StatsTrait
{
    /**
     * Get a Stats component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Stats\Stats
     */
    public function getStats()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_STATS, true);
    }
}
