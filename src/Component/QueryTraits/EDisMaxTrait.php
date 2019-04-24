<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\EdisMax;

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
    public function getEDisMax(): EdisMax
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_EDISMAX, true);
    }
}
