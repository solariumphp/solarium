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
namespace Solarium\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\RequestBuilder\Component\FacetSet as RequestBuilder;
use Solarium\QueryType\Select\ResponseParser\Component\FacetSet as ResponseParser;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\QueryType\Select\Query\Component\Facet\Facet;

/**
 * MoreLikeThis component
 *
 * @link http://wiki.apache.org/solr/MoreLikeThis
 */
class FacetSet extends Component
{
    /**
     * Facet type field
     */
    const FACET_FIELD = 'field';

    /**
     * Facet type query
     */
    const FACET_QUERY = 'query';

    /**
     * Facet type multiquery
     */
    const FACET_MULTIQUERY = 'multiquery';

    /**
     * Facet type range
     */
    const FACET_RANGE = 'range';

    /**
     * Facet type pivot
     */
    const FACET_PIVOT = 'pivot';

    /**
     * Facet type mapping
     *
     * @var array
     */
    protected $facetTypes = array(
        self::FACET_FIELD => 'Solarium\QueryType\Select\Query\Component\Facet\Field',
        self::FACET_QUERY => 'Solarium\QueryType\Select\Query\Component\Facet\Query',
        self::FACET_MULTIQUERY => 'Solarium\QueryType\Select\Query\Component\Facet\MultiQuery',
        self::FACET_RANGE => 'Solarium\QueryType\Select\Query\Component\Facet\Range',
        self::FACET_PIVOT => 'Solarium\QueryType\Select\Query\Component\Facet\Pivot',
    );

    /**
     * Default options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Facets
     *
     * @var Facet[]
     */
    protected $facets = array();

    /**
     * Get component type
     *
     * @return string
     */
    public function getType()
    {
        return SelectQuery::COMPONENT_FACETSET;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get a response parser for this query
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

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
        if (isset($this->options['facet'])) {
            foreach ($this->options['facet'] as $key => $config) {
                if (!isset($config['key'])) {
                    $config['key'] = $key;
                }

                $this->addFacet($config);
            }
        }
    }

    /**
     * Allow extraction of facets without having to define
     * them on the query
     *
     * @param boolean $extract
     * @return self Provides fluent interface
     */
    public function setExtractFromResponse($extract)
    {
        return $this->setOption('extractfromresponse', $extract);
    }

    /**
     * Get the extractfromresponse option value
     *
     * @return boolean
     */
    public function getExtractFromResponse()
    {
        return $this->getOption('extractfromresponse');
    }

    /**
     * Limit the terms for faceting by a prefix
     *
     * This is a global value for all facets in this facetset
     *
     * @param  string $prefix
     * @return self   Provides fluent interface
     */
    public function setPrefix($prefix)
    {
        return $this->setOption('prefix', $prefix);
    }

    /**
     * Get the facet prefix
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->getOption('prefix');
    }

    /**
     * Set the facet sort order
     *
     * Use one of the SORT_* constants as the value
     *
     * This is a global value for all facets in this facetset
     *
     * @param  string $sort
     * @return self   Provides fluent interface
     */
    public function setSort($sort)
    {
        return $this->setOption('sort', $sort);
    }

    /**
     * Get the facet sort order
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }

    /**
     * Set the facet limit
     *
     *  This is a global value for all facets in this facetset
     *
     * @param  int  $limit
     * @return self Provides fluent interface
     */
    public function setLimit($limit)
    {
        return $this->setOption('limit', $limit);
    }

    /**
     * Get the facet limit
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet mincount
     *
     * This is a global value for all facets in this facetset
     *
     * @param  int  $minCount
     * @return self Provides fluent interface
     */
    public function setMinCount($minCount)
    {
        return $this->setOption('mincount', $minCount);
    }

    /**
     * Get the facet mincount
     *
     * This is a global value for all facets in this facetset
     *
     * @return int
     */
    public function getMinCount()
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the missing count option
     *
     * This is a global value for all facets in this facetset
     *
     * @param  boolean $missing
     * @return self    Provides fluent interface
     */
    public function setMissing($missing)
    {
        return $this->setOption('missing', $missing);
    }

    /**
     * Get the facet missing option
     *
     * This is a global value for all facets in this facetset
     *
     * @return boolean
     */
    public function getMissing()
    {
        return $this->getOption('missing');
    }

    /**
     * Add a facet
     *
     * @throws InvalidArgumentException
     * @param  \Solarium\QueryType\Select\Query\Component\Facet\Facet|array $facet
     * @return self                                                         Provides fluent interface
     */
    public function addFacet($facet)
    {
        if (is_array($facet)) {
            $facet = $this->createFacet($facet['type'], $facet, false);
        }

        $key = $facet->getKey();

        if (0 === strlen($key)) {
            throw new InvalidArgumentException('A facet must have a key value');
        }

        //double add calls for the same facet are ignored, but non-unique keys cause an exception
        //@todo add trigger_error with a notice for double add calls?
        if (array_key_exists($key, $this->facets) && $this->facets[$key] !== $facet) {
            throw new InvalidArgumentException('A facet must have a unique key value within a query');
        } else {
            $this->facets[$key] = $facet;
        }

        return $this;
    }

    /**
     * Add multiple facets
     *
     * @param  array $facets
     * @return self  Provides fluent interface
     */
    public function addFacets(array $facets)
    {
        foreach ($facets as $key => $facet) {

            // in case of a config array: add key to config
            if (is_array($facet) && !isset($facet['key'])) {
                $facet['key'] = $key;
            }

            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Get a facet
     *
     * @param  string $key
     * @return string
     */
    public function getFacet($key)
    {
        if (isset($this->facets[$key])) {
            return $this->facets[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all facets
     *
     * @return Facet[]
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Remove a single facet
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param  string|\Solarium\QueryType\Select\Query\Component\Facet\Facet $facet
     * @return self                                                          Provides fluent interface
     */
    public function removeFacet($facet)
    {
        if (is_object($facet)) {
            $facet = $facet->getKey();
        }

        if (isset($this->facets[$facet])) {
            unset($this->facets[$facet]);
        }

        return $this;
    }

    /**
     * Remove all facets
     *
     * @return self Provides fluent interface
     */
    public function clearFacets()
    {
        $this->facets = array();

        return $this;
    }

    /**
     * Set multiple facets
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     */
    public function setFacets($facets)
    {
        $this->clearFacets();
        $this->addFacets($facets);
    }

    /**
     * Create a facet instance
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the facet
     * and it will be added to this query.
     * If you supply an options array/object that contains a key the facet will also be added to the query.
     *
     * When no key is supplied the facet cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addFacet method.
     *
     * @throws OutOfBoundsException
     * @param  string               $type
     * @param  array|object|null    $options
     * @param  boolean              $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\Facet
     */
    public function createFacet($type, $options = null, $add = true)
    {
        $type = strtolower($type);

        if (!isset($this->facetTypes[$type])) {
            throw new OutOfBoundsException("Facettype unknown: " . $type);
        }

        $class = $this->facetTypes[$type];

        if (is_string($options)) {
            /** @var \Solarium\QueryType\Select\Query\Component\Facet\Facet $facet */
            $facet = new $class;
            $facet->setKey($options);
        } else {
            $facet = new $class($options);
        }

        if ($add && $facet->getKey() !== null) {
            $this->addFacet($facet);
        }

        return $facet;
    }

    /**
     * Get a facet field instance
     *
     * @param  mixed       $options
     * @param  bool        $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\Field
     */
    public function createFacetField($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_FIELD, $options, $add);
    }

    /**
     * Get a facet query instance
     *
     * @param  mixed       $options
     * @param  bool        $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\Query
     */
    public function createFacetQuery($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_QUERY, $options, $add);
    }

    /**
     * Get a facet multiquery instance
     *
     * @param  mixed            $options
     * @param  bool             $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\MultiQuery
     */
    public function createFacetMultiQuery($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_MULTIQUERY, $options, $add);
    }

    /**
     * Get a facet range instance
     *
     * @param  mixed       $options
     * @param  bool        $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\Range
     */
    public function createFacetRange($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_RANGE, $options, $add);
    }

    /**
     * Get a facet pivot instance
     *
     * @param  mixed       $options
     * @param  bool        $add
     * @return \Solarium\QueryType\Select\Query\Component\Facet\Pivot
     */
    public function createFacetPivot($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_PIVOT, $options, $add);
    }
}
