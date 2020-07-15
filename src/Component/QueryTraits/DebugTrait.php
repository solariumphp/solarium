<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Debug;

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
    public function getDebug(): Debug
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DEBUG, true);
    }
}
