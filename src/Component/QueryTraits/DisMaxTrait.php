<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\DisMax;

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
    public function getDisMax(): DisMax
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DISMAX, true);
    }
}
