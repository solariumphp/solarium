<?php

namespace Solarium\Component\Facet;

/**
 * Exclude tags for facets.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 */
interface ExcludeTagsInterface
{
    /**
     * Add an exclude tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function addExclude(string $tag): self;

    /**
     * Add multiple exclude tags.
     *
     * @param array $excludes
     *
     * @return self Provides fluent interface
     */
    public function addExcludes(array $excludes): self;

    /**
     * Get all excludes.
     *
     * @return array
     */
    public function getExcludes(): array;

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude(string $exclude): self;

    /**
     * Remove all excludes.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes(): self;

    /**
     * Set multiple excludes.
     *
     * This overwrites any existing excludes
     *
     * @param array $excludes
     *
     * @return self Provides fluent interface
     */
    public function setExcludes(array $excludes): self;
}
