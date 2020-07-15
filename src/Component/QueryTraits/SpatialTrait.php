<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Spatial;

/**
 * Trait query types supporting components.
 */
trait SpatialTrait
{
    /**
     * Get a Spatial component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spatial
     */
    public function getSpatial(): Spatial
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SPATIAL, true);
    }
}
