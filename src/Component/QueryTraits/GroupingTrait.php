<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Grouping;

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
    public function getGrouping(): Grouping
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING, true);
    }
}
