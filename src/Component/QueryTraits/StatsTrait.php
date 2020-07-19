<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Stats\Stats;

/**
 * Trait query types supporting components.
 */
trait StatsTrait
{
    /**
     * Get a Stats component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Stats\Stats
     */
    public function getStats(): Stats
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_STATS, true);
    }
}
