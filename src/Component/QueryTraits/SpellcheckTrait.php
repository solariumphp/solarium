<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Trait query types supporting components.
 */
trait SpellcheckTrait
{
    /**
     * Get a spellcheck component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spellcheck
     */
    public function getSpellcheck()
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SPELLCHECK, true);
    }
}
