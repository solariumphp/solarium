<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Select\Query\Component\Facet;

use Solarium\QueryType\Select\Query\Component\FacetSet;
use Solarium\QueryType\Select\Query\Component\Facet\Query as FacetQuery;
use Solarium\Exception\InvalidArgumentException;

/**
 * Facet MultiQuery
 *
 * This is a 'virtual' querytype that combines multiple facet queries into a
 * single resultset
 */
class MultiQuery extends Facet
{
    /**
     * Facet query objects
     *
     * @var FacetQuery[]
     */
    protected $facetQueries = array();

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'query':
                    if (!is_array($value)) {
                        $value = array(array('query' => $value));
                    }
                    $this->addQueries($value);
                    break;
            }
        }
    }

    /**
     * Get the facet type
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_MULTIQUERY;
    }

    /**
     * Create a new facetQuery
     *
     * Convenience method so you don't need to manually create facetquery
     * objects.
     *
     * @param  string $key
     * @param  string $query
     * @param  array  $excludes
     * @return self   Provides fluent interface
     */
    public function createQuery($key, $query, $excludes = array())
    {
        // merge excludes with shared excludes
        $excludes = array_merge($this->getExcludes(), $excludes);

        $facetQuery = new Query;
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        return $this->addQuery($facetQuery);
    }

    /**
     * Add a facetquery
     *
     * Supports a facetquery instance or a config array, in that case a new
     * facetquery instance wil be created based on the options.
     *
     * @throws InvalidArgumentException
     * @param  Query|array              $facetQuery
     * @return self                     Provides fluent interface
     */
    public function addQuery($facetQuery)
    {
        if (is_array($facetQuery)) {
            $facetQuery = new Query($facetQuery);
        }

        $key = $facetQuery->getKey();

        if (0 === strlen($key)) {
            throw new InvalidArgumentException('A facetquery must have a key value');
        }

        if (array_key_exists($key, $this->facetQueries)) {
            throw new InvalidArgumentException('A query must have a unique key value within a multiquery facet');
        }

        // forward shared excludes
        $facetQuery->addExcludes($this->getExcludes());

        $this->facetQueries[$key] = $facetQuery;

        return $this;
    }

    /**
     * Add multiple facetqueries
     *
     * @param  array $facetQueries Instances or config array
     * @return self  Provides fluent interface
     */
    public function addQueries(array $facetQueries)
    {
        foreach ($facetQueries as $key => $facetQuery) {

            // in case of a config array: add key to config
            if (is_array($facetQuery) && !isset($facetQuery['key'])) {
                $facetQuery['key'] = $key;
            }

            $this->addQuery($facetQuery);
        }

        return $this;
    }

    /**
     * Get a facetquery
     *
     * @param  string $key
     * @return string
     */
    public function getQuery($key)
    {
        if (isset($this->facetQueries[$key])) {
            return $this->facetQueries[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all facetqueries
     *
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->facetQueries;
    }

    /**
     * Remove a single facetquery
     *
     * You can remove a facetquery by passing its key or the facetquery instance.
     *
     * @param  string|Query $query
     * @return self         Provides fluent interface
     */
    public function removeQuery($query)
    {
        if (is_object($query)) {
            $query = $query->getKey();
        }

        if (isset($this->facetQueries[$query])) {
            unset($this->facetQueries[$query]);
        }

        return $this;
    }

    /**
     * Remove all facetqueries
     *
     * @return self Provides fluent interface
     */
    public function clearQueries()
    {
        $this->facetQueries = array();

        return $this;
    }

    /**
     * Set multiple facetqueries
     *
     * This overwrites any existing facetqueries
     *
     * @param  array $facetQueries
     * @return self  Provides fluent interface
     */
    public function setQueries($facetQueries)
    {
        $this->clearQueries();

        return $this->addQueries($facetQueries);
    }

    /**
     * Add an exclude tag
     *
     * Excludes added to the MultiQuery facet a shared by all underlying
     * FacetQueries, so they must be forwarded to any existing instances.
     *
     * If you don't want to share an exclude use the addExclude method of a
     * specific FacetQuery instance instead.
     *
     * @param  string $tag
     * @return self   Provides fluent interface
     */
    public function addExclude($tag)
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->addExclude($tag);
        }

        return parent::addExclude($tag);
    }

    /**
     * Remove a single exclude tag
     *
     * Excludes added to the MultiQuery facet a shared by all underlying
     * FacetQueries, so changes must be forwarded to any existing instances.
     *
     * If you don't want this use the removeExclude method of a
     * specific FacetQuery instance instead.
     *
     * @param  string $exclude
     * @return self   Provides fluent interface
     */
    public function removeExclude($exclude)
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->removeExclude($exclude);
        }

        return parent::removeExclude($exclude);
    }

    /**
     * Remove all excludes
     *
     * Excludes added to the MultiQuery facet a shared by all underlying
     * FacetQueries, so changes must be forwarded to any existing instances.
     *
     * If you don't want this use the clearExcludes method of a
     * specific FacetQuery instance instead.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes()
    {
        foreach ($this->facetQueries as $facetQuery) {
            $facetQuery->clearExcludes();
        }

        return parent::clearExcludes();
    }
}
