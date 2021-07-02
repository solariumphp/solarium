<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\QueryTraits;

use Solarium\Component\Analytics\Analytics;
use Solarium\Component\ComponentAwareQueryInterface;

/**
 * Analytics Trait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait AnalyticsTrait
{
    /**
     * @return \Solarium\Component\Analytics\Analytics
     */
    public function getAnalytics(): Analytics
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_ANALYTICS, true);
    }
}
