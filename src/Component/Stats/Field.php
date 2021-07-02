<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Stats;

use Solarium\Core\Configurable;

/**
 * Stats component field class.
 */
class Field extends Configurable
{
    use FacetsTrait;

    /**
     * pivot facets for these stats.
     *
     * @var array
     */
    protected $pivots = [];

    /**
     * Get key value.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $value): self
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Add pivot.
     *
     * @param string $pivot
     *
     * @return self Provides fluent interface
     */
    public function addPivot(string $pivot): self
    {
        $this->pivots[$pivot] = true;

        return $this;
    }

    /**
     * Specify multiple Pivots.
     *
     * @param string|array $pivots can be an array or string with comma
     *                             separated facetnames
     *
     * @return self Provides fluent interface
     */
    public function addPivots($pivots): self
    {
        if (\is_string($pivots)) {
            $pivots = explode(',', $pivots);
            $pivots = array_map('trim', $pivots);
        }

        foreach ($pivots as $facet) {
            $this->addPivot($facet);
        }

        return $this;
    }

    /**
     * Remove a pivot facet from the pivot list.
     *
     * @param string $pivot
     *
     * @return self Provides fluent interface
     */
    public function removePivot(string $pivot): self
    {
        if (isset($this->pivots[$pivot])) {
            unset($this->pivots[$pivot]);
        }

        return $this;
    }

    /**
     * Remove all pivot facets from the pivot list.
     *
     * @return self Provides fluent interface
     */
    public function clearPivots(): self
    {
        $this->pivots = [];

        return $this;
    }

    /**
     * Get the list of pivot facets.
     *
     * @return array
     */
    public function getPivots(): array
    {
        return array_keys($this->pivots);
    }

    /**
     * Set multiple pivot facets.
     *
     * This overwrites any existing pivots
     *
     * @param array|string $pivots can be an array or string with comma
     *                             separated facetnames
     *
     * @return self Provides fluent interface
     */
    public function setPivots($pivots): self
    {
        $this->clearPivots();
        $this->addPivots($pivots);

        return $this;
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'facet':
                    $this->setFacets($value);
                    break;
                case 'pivot':
                    $this->setPivots($value);
                    break;
            }
        }
    }
}
