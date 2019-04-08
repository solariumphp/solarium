<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Spellcheck;

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
    public function getSpellcheck(): Spellcheck
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SPELLCHECK, true);
    }
}
