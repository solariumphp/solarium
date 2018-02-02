<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait SuggesterTrait
{
    /**
     * Get a suggest component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Suggester
     */
    public function getSuggester()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SUGGESTER, true);
    }
}
