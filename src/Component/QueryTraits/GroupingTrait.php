<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait GroupingTrait
{
    /**
     * Get a grouping component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Grouping
     */
    public function getGrouping()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING, true);
    }
}
