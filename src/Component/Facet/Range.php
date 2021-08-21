<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet range.
 *
 * @see https://solr.apache.org/guide/faceting.html#range-faceting
 */
class Range extends AbstractRange
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_RANGE;
    }

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount(int $minCount): self
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        parent::init();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'exclude':
                    $this->getLocalParameters()->addExcludes($value);
                    break;
                case 'pivot':
                    $this->setPivot(new Pivot($value));
            }
        }
    }
}
