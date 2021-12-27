<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

/**
 * Pivot Facet MinCount trait.
 */
trait PivotMinCountTrait
{
    /**
     * Set the minimum number of documents that need to match in order for the facet to be included in results.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setPivotMinCount(int $minCount): self
    {
        $this->setOption('pivot.mincount', $minCount);

        return $this;
    }

    /**
     * Get the minimum number of documents that need to match in order for the facet to be included in results.
     *
     * @return int|null
     */
    public function getPivotMinCount(): ?int
    {
        return $this->getOption('pivot.mincount');
    }
}
