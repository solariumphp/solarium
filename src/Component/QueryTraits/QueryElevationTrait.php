<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryElevation;

/**
 * Trait query types supporting components.
 */
trait QueryElevationTrait
{
    /**
     * Get a QueryElevation component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\QueryElevation
     */
    public function getQueryElevation(): QueryElevation
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION, true);
    }
}
