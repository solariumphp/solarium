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
    protected $facetQueries = [];

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
     * Create a new facetQuery.
     *
     * Convenience method so you don't need to manually create facetquery
     * objects.
     *
     * @param string $key
     * @param string $query
     * @param array  $excludes
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function createQuery(string $key, string $query, array $excludes = []): self
    {
        // merge excludes with shared excludes
        $excludes = array_merge($this->getLocalParameters()->getExcludes(), $excludes);

        $facetQuery = new Query();
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->getLocalParameters()->addExcludes($excludes);

        return $this->addQuery($facetQuery);
    }

    /**
     * Add a facetquery.
     *
     * Supports a facetquery instance or a config array, in that case a new
     * facetquery instance wil be created based on the options.
     *
     * @param Query|array $facetQuery
     *
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addQuery($facetQuery): self
    {
        if (\is_array($facetQuery)) {
            $facetQuery = new Query($facetQuery);
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
     * Add multiple facetqueries.
     *
     * @param array $facetQueries Instances or config array
     *
     * @return self Provides fluent interface
     */
    public function addQueries(array $facetQueries): self
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
     * Get a facetquery.
     *
     * @param string $key
     *
     * @return Query|null
     */
    public function getQuery($key): ?Query
    {
        return $this->facetQueries[$key] ?? null;
    }

    /**
     * Get all facetqueries.
     *
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->facetQueries;
    }

    /**
     * Remove a single facetquery.
     *
     * You can remove a facetquery by passing its key or the facetquery instance.
     *
     * @param string|Query $query
     *
     * @return self Provides fluent interface
     */
    public function removeQuery($query): self
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
     * Remove all facetqueries.
     *
     * @return self Provides fluent interface
     */
    public function clearQueries(): self
    {
        $this->facetQueries = [];

        return $this;
    }

    /**
     * Set multiple facetqueries.
     *
     * This overwrites any existing facetqueries
     *
     * @param array $facetQueries
     *
     * @return self Provides fluent interface
     */
    public function setQueries(array $facetQueries): self
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
     *
     * @return self Provides fluent interface
     */
    public function addExclude(string $exclude): self
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
     * @param array|string $excludes array or string with comma separated exclude tags
     *
     * @return self Provides fluent interface
     */
    public function addExcludes($excludes): self
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
     * @param array|string $excludes array or string with comma separated exclude tags
     *
     * @return self Provides fluent interface
     */
    public function setExcludes($excludes): self
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
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude(string $exclude): self
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
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes(): self
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
     * @return array
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
    protected function init()
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
