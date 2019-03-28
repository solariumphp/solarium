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
    public function addExclude(string $tag): ExcludeTagsInterface;

    /**
     * Add multiple exclude tags.
     *
     * @param array $excludes
     *
     * @return self Provides fluent interface
     */
    public function addExcludes(array $excludes): ExcludeTagsInterface;

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
    public function removeExclude(string $exclude): ExcludeTagsInterface;

    /**
     * Remove all excludes.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes(): ExcludeTagsInterface;

    /**
     * Set multiple excludes.
     *
     * This overwrites any existing excludes
     *
     * @param array $excludes
     */
    public function setExcludes(array $excludes): ExcludeTagsInterface;
}
