<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait TermsTrait
{
    /**
     * Get a terms component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Terms
     */
    public function getTerms()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_TERMS, true);
    }
}
