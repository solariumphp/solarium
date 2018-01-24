<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait FacetSetTrait
{
    /**
     * Get a FacetSet component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\FacetSet
     */
    public function getFacetSet()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_FACETSET, true);
    }
}
