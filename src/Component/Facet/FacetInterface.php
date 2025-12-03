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
     */
    public function setKey(string $key): static;

    /**
     * Add an exclude tag.
     *
     * @param string $exclude
     */
    public function addExclude(string $exclude): static;

    /**
     * Add multiple exclude tags.
     *
     * @param string[]|string $excludes array or string with comma separated exclude tags
     */
    public function addExcludes(array|string $excludes): static;

    /**
     * Set the list of exclude tags.
     *
     * This overwrites any existing exclude tags.
     *
     * @param string[]|string $excludes
     */
    public function setExcludes(array|string $excludes): static;

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     */
    public function removeExclude(string $exclude): static;

    /**
     * Remove all exclude tags.
     */
    public function clearExcludes(): static;

    /**
     * Get the list of exclude tags.
     *
     * @return string[]
     */
    public function getExcludes(): array;
}
