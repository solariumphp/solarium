<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Terms;

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
    public function getTerms(): Terms
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_TERMS, true);
    }
}
