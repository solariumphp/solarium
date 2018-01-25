<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait EDisMaxTrait
{
    /**
     * Get a EdisMax component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\EdisMax
     */
    public function getEDisMax()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_EDISMAX, true);
    }
}
