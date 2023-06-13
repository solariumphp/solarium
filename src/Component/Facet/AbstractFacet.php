<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Core\Configurable;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;

/**
 * Facet base class.
 *
 * @see https://solr.apache.org/guide/faceting.html
 */
abstract class AbstractFacet extends Configurable implements FacetInterface
{
    use LocalParametersTrait;

    /**
     * Must be implemented by the facet types and return one of the constants.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getLocalParameters()->getKeys()[0] ?? null;
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $key): self
    {
        $this->getLocalParameters()->setKey($key);

        return $this;
    }

    /**
     * Add an exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function addExclude(string $exclude)
    {
        $this->getLocalParameters()->setExclude($exclude);

        return $this;
    }

    /**
     * Add multiple exclude tags.
     *
     * @param array|string $excludes array or string with comma separated exclude tags
     *
     * @return self Provides fluent interface
     */
    public function addExcludes($excludes)
    {
        if (\is_string($excludes)) {
            $excludes = preg_split('/(?<!\\\\),/', $excludes);
        }

        $this->getLocalParameters()->addExcludes($excludes);

        return $this;
    }

    /**
     * Set the list of exclude tags.
     *
     * This overwrites any existing exclude tags.
     *
     * @param array|string $excludes
     *
     * @return self Provides fluent interface
     */
    public function setExcludes($excludes)
    {
        $this->clearExcludes()->addExcludes($excludes);

        return $this;
    }

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude(string $exclude)
    {
        $this->getLocalParameters()->removeExclude($exclude);

        return $this;
    }

    /**
     * Remove all exclude tags.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes()
    {
        $this->getLocalParameters()->clearExcludes();

        return $this;
    }

    /**
     * Get the list of exclude tags.
     *
     * @return array
     */
    public function getExcludes(): array
    {
        return $this->getLocalParameters()->getExcludes();
    }
}
