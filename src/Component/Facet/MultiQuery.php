<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\FacetSetInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;

/**
 * Facet MultiQuery.
 *
 * This is a 'virtual' querytype that combines multiple facet queries into a
 * single resultset
 */
class MultiQuery extends AbstractFacet
{
    /**
     * Facet query objects.
     *
     * @var FacetQuery[]
     */
    protected array $facetQueries = [];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_MULTIQUERY;
    }

    /**
     * Create a new MultiQuery.
     *
     * Convenience method so you don't need to manually create MultiQuery
     * objects.
     *
     * @param string $key
     * @param string $query
     * @param array  $excludes
     *
     * @throws OutOfBoundsException
     */
    public function createQuery(string $key, string $query, array $excludes = []): static
    {
        // merge excludes with shared excludes
        $excludes = array_merge($this->getLocalParameters()->getExcludes(), $excludes);

        $facetQuery = new FacetQuery();
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->getLocalParameters()->addExcludes($excludes);

        return $this->addQuery($facetQuery);
    }

    /**
     * Add a FacetQuery.
     *
     * Supports a FacetQuery instance or a config array, in that case a new
     * facetquery instance wil be created based on the options.
     *
     * @param FacetQuery|array $facetQuery
     *
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function addQuery(FacetQuery|array $facetQuery): static
    {
        if (\is_array($facetQuery)) {
            $facetQuery = new FacetQuery($facetQuery);
        }

        $key = $facetQuery->getKey();

        if (null === $key || 0 === \strlen($key)) {
            throw new InvalidArgumentException('A facetquery must have a key value');
        }

        if (\array_key_exists($key, $this->facetQueries)) {
            throw new InvalidArgumentException('A query must have a unique key value within a multiquery facet');
        }

        // forward shared excludes
        $excludes = $this->getLocalParameters()->getExcludes();

        if (0 !== \count($excludes)) {
            $facetQuery->getLocalParameters()->addExcludes($excludes);
        }

        $this->facetQueries[$key] = $facetQuery;

        return $this;
    }

    /**
     * Add multiple FacetQueries.
     *
     * @param FacetQuery[]|array[] $facetQueries FacetQuery instances or config arrays
     */
    public function addQueries(array $facetQueries): static
    {
        foreach ($facetQueries as $key => $facetQuery) {
            // in case of a config array: add key to config
            if (\is_array($facetQuery) && !isset($facetQuery['local_key'])) {
                $facetQuery['local_key'] = (string) $key;
            }

            $this->addQuery($facetQuery);
        }

        return $this;
    }

    /**
     * Get a FacetQuery.
     *
     * @param string $key
     *
     * @return FacetQuery|null
     */
    public function getQuery(string $key): ?FacetQuery
    {
        return $this->facetQueries[$key] ?? null;
    }

    /**
     * Get all FacetQueries.
     *
     * @return FacetQuery[]
     */
    public function getQueries(): array
    {
        return $this->facetQueries;
    }

    /**
     * Remove a single FacetQuery.
     *
     * You can remove a FacetQuery by passing its key or the FacetQuery instance.
     *
     * @param string|FacetQuery $query
     */
    public function removeQuery(string|FacetQuery $query): static
    {
        if (\is_object($query)) {
            $query = $query->getKey();
        }

        if (isset($this->facetQueries[$query])) {
            unset($this->facetQueries[$query]);
        }

        return $this;
    }

    /**
     * Remove all FacetQueries.
     */
    public function clearQueries(): static
    {
        $this->facetQueries = [];

        return $this;
    }

    /**
     * Set multiple FacetQueries.
     *
     * This overwrites any existing FacetQueries.
     *
     * @param FacetQuery[]|array[] $facetQueries FacetQuery instances or config arrays
     */
    public function setQueries(array $facetQueries): static
    {
        $this->clearQueries();

        return $this->addQueries($facetQueries);
    }

    /**
     * Add an exclude tag.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so they must be forwarded to any existing instances.
     *
     * If you don't want to share an exclude use the addExclude method of a
     * specific FacetQuery instance instead.
     *
     * @param string $exclude
     */
    public function addExclude(string $exclude): static
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->addExclude($exclude);
        }

        $this->getLocalParameters()->setExclude($exclude);

        return $this;
    }

    /**
     * Add exclude tags.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so they must be forwarded to any existing instances.
     *
     * If you don't want to share excludes use the addExcludes method of a
     * specific FacetQuery instance instead.
     *
     * @param string[]|string $excludes array or string with comma separated exclude tags
     */
    public function addExcludes(array|string $excludes): static
    {
        if (\is_string($excludes)) {
            $excludes = preg_split('/(?<!\\\\),/', $excludes);
        }

        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->addExcludes($excludes);
        }

        $this->getLocalParameters()->addExcludes($excludes);

        return $this;
    }

    /**
     * Set the list of exclude tags.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so they must be forwarded to any existing instances.
     *
     * If you don't want to share excludes use the setExcludes method of a
     * specific FacetQuery instance instead.
     *
     * @param string[]|string $excludes array or string with comma separated exclude tags
     */
    public function setExcludes(array|string $excludes): static
    {
        if (\is_string($excludes)) {
            $excludes = preg_split('/(?<!\\\\),/', $excludes);
        }

        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->setExcludes($excludes);
        }

        $this->getLocalParameters()->setExcludes($excludes);

        return $this;
    }

    /**
     * Remove a single exclude tag.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so changes must be forwarded to any existing instances.
     *
     * If you don't want this use the removeExclude method of a
     * specific FacetQuery instance instead.
     */
    public function removeExclude(string $exclude): static
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->removeExclude($exclude);
        }

        $this->getLocalParameters()->removeExclude($exclude);

        return $this;
    }

    /**
     * Remove all exclude tags.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so changes must be forwarded to any existing instances.
     *
     * If you don't want this use the clearExcludes method of a
     * specific FacetQuery instance instead.
     */
    public function clearExcludes(): static
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->clearExcludes();
        }

        $this->getLocalParameters()->clearExcludes();

        return $this;
    }

    /**
     * Get the list of exclude tags.
     *
     * Excludes added to the MultiQuery facet are shared by all underlying
     * FacetQueries, so they must be forwarded to any existing instances.
     *
     * If you don't want to share excludes use the getExcludes method of a
     * specific FacetQuery instance instead.
     *
     * @return string[]
     */
    public function getExcludes(): array
    {
        return $this->getLocalParameters()->getExcludes();
    }

    /**
     * Initialize options.
     *
     * {@internal The 'query' option needs additional setup work.}
     */
    protected function init(): void
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'query':
                    if (!\is_array($value)) {
                        $value = [['query' => $value]];
                    }
                    $this->addQueries($value);
                    break;
            }
        }
    }
}
