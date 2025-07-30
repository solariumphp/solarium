<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\TermVector;

/**
 * Trait query types supporting components.
 */
trait TermVectorTrait
{
    /**
     * Get a term vector component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return TermVector
     */
    public function getTermVector(): TermVector
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_TERMVECTOR, true);
    }
}
