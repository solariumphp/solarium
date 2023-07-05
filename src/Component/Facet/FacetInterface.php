<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

/**
 * Facet interface.
 *
 * @see https://solr.apache.org/guide/faceting.html
 */
interface FacetInterface
{
    /**
     * Must be implemented by the facet types and return one of the constants.
     *
     * @abstract
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey(): ?string;

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $key): self;

    /**
     * Add an exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function addExclude(string $exclude): self;

    /**
     * Add multiple exclude tags.
     *
     * @param array|string $excludes array or string with comma separated exclude tags
     *
     * @return self Provides fluent interface
     */
    public function addExcludes($excludes): self;

    /**
     * Set the list of exclude tags.
     *
     * This overwrites any existing exclude tags.
     *
     * @param array|string $excludes
     *
     * @return self Provides fluent interface
     */
    public function setExcludes($excludes): self;

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude(string $exclude): self;

    /**
     * Remove all exclude tags.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes(): self;

    /**
     * Get the list of exclude tags.
     *
     * @return array
     */
    public function getExcludes();
}
