<?php

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis;

/**
 * Trait query types supporting components.
 */
trait MoreLikeThisTrait
{
    /**
     * Get a MoreLikeThis component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\MoreLikeThis
     */
    public function getMoreLikeThis(): MoreLikeThis
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS, true);
    }
}
