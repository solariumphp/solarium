<?php

namespace Solarium\Component\Traits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait DebugTrait
{

    /**
     * Get a Debug component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Debug
     */
    public function getDebug()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DEBUG, true);
    }

}
