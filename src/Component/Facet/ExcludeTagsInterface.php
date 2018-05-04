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
    public function addExclude($tag);

    /**
     * Add multiple exclude tags.
     *
     * @param array $excludes
     *
     * @return self Provides fluent interface
     */
    public function addExcludes(array $excludes);

    /**
     * Get all excludes.
     *
     * @return array
     */
    public function getExcludes();

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude($exclude);

    /**
     * Remove all excludes.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes();

    /**
     * Set multiple excludes.
     *
     * This overwrites any existing excludes
     *
     * @param array $excludes
     */
    public function setExcludes($excludes);
}
