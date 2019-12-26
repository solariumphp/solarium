<?php

declare(strict_types=1);

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
