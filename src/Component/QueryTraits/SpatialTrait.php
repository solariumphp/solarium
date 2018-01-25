<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait SpatialTrait
{
    /**
     * Get a Spatial component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spatial
     */
    public function getSpatial()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SPATIAL, true);
    }
}
