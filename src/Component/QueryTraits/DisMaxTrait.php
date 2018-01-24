<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait DisMaxTrait
{
    /**
     * Get a DisMax component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\DisMax
     */
    public function getDisMax()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DISMAX, true);
    }
}
