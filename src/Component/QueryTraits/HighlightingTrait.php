<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Highlighting\Highlighting;

/**
 * Trait query types supporting components.
 */
trait HighlightingTrait
{
    /**
     * Get a highlighting component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Highlighting\Highlighting
     */
    public function getHighlighting(): Highlighting
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING, true);
    }
}
