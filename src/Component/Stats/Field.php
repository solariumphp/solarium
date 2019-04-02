<?php

namespace Solarium\Component\Stats;

use Solarium\Core\Configurable;

/**
 * Stats component field class.
 */
class Field extends Configurable
{
    /**
     * Field facets (for stats).
     *
     * @var array
     */
    protected $facets = [];

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
     * Specify a facet to return in the resultset.
     *
     * @param string $facet
     *
     * @return self Provides fluent interface
     */
    public function addFacet(string $facet): self
    {
        $this->facets[$facet] = true;

        return $this;
    }

    /**
     * Specify multiple facets to return in the resultset.
     *
     * @param string|array $facets can be an array or string with comma
     *                             separated facetnames
     *
     * @return self Provides fluent interface
     */
    public function addFacets($facets): self
    {
        if (is_string($facets)) {
            $facets = explode(',', $facets);
            $facets = array_map('trim', $facets);
        }

        foreach ($facets as $facet) {
            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Remove a facet from the facet list.
     *
     * @param string $facet
     *
     * @return self Provides fluent interface
     */
    public function removeFacet(string  $facet): self
    {
        if (isset($this->facets[$facet])) {
            unset($this->facets[$facet]);
        }

        return $this;
    }

    /**
     * Remove all facets from the facet list.
     *
     * @return self Provides fluent interface
     */
    public function clearFacets(): self
    {
        $this->facets = [];

        return $this;
    }

    /**
     * Get the list of facets.
     *
     * @return array
     */
    public function getFacets(): array
    {
        return array_keys($this->facets);
    }

    /**
     * Set multiple facets.
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     *
     * @return self Provides fluent interface
     */
    public function setFacets(array $facets): self
    {
        $this->clearFacets();
        $this->addFacets($facets);

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
        if (is_string($pivots)) {
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
     * @param array $pivots
     *
     * @return self Provides fluent interface
     */
    public function setPivots(array $pivots): self
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
