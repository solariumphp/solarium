<?php

namespace Solarium\Component;

use Solarium\Component\Facet\AbstractFacet;
use Solarium\Component\RequestBuilder\FacetSet as RequestBuilder;
use Solarium\Component\ResponseParser\FacetSet as ResponseParser;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;

/**
 * MoreLikeThis component.
 *
 * @see http://wiki.apache.org/solr/MoreLikeThis
 */
class FacetSet extends AbstractComponent
{
    /**
     * Facet type field.
     */
    const FACET_FIELD = 'field';

    /**
     * Facet type query.
     */
    const FACET_QUERY = 'query';

    /**
     * Facet type multiquery.
     */
    const FACET_MULTIQUERY = 'multiquery';

    /**
     * Facet type range.
     */
    const FACET_RANGE = 'range';

    /**
     * Facet type pivot.
     */
    const FACET_PIVOT = 'pivot';

    /**
     * Facet type interval.
     */
    const FACET_INTERVAL = 'interval';

    /**
     * Facet type mapping.
     *
     * @var array
     */
    protected $facetTypes = [
        self::FACET_FIELD => 'Solarium\Component\Facet\Field',
        self::FACET_QUERY => 'Solarium\Component\Facet\Query',
        self::FACET_MULTIQUERY => 'Solarium\Component\Facet\MultiQuery',
        self::FACET_RANGE => 'Solarium\Component\Facet\Range',
        self::FACET_PIVOT => 'Solarium\Component\Facet\Pivot',
        self::FACET_INTERVAL => 'Solarium\Component\Facet\Interval',
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Facets.
     *
     * @var AbstractFacet[]
     */
    protected $facets = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_FACETSET;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Allow extraction of facets without having to define
     * them on the query.
     *
     * @param bool $extract
     *
     * @return self Provides fluent interface
     */
    public function setExtractFromResponse($extract)
    {
        return $this->setOption('extractfromresponse', $extract);
    }

    /**
     * Get the extractfromresponse option value.
     *
     * @return bool
     */
    public function getExtractFromResponse()
    {
        return $this->getOption('extractfromresponse');
    }

    /**
     * Limit the terms for faceting by a prefix.
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setPrefix($prefix)
    {
        return $this->setOption('prefix', $prefix);
    }

    /**
     * Get the facet prefix.
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
     * Limit the terms for faceting by a string they must contain.
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $contains
     *
     * @return self Provides fluent interface
     */
    public function setContains($contains)
    {
        return $this->setOption('contains', $contains);
    }

    /**
     * Get the facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getContains()
    {
        return $this->getOption('contains');
    }

    /**
     * Case sensitivity of matching string that facet terms must contain.
     *
     * This is a global value for all facets in this facetset
     *
     * @param bool $containsIgnoreCase
     *
     * @return self Provides fluent interface
     */
    public function setContainsIgnoreCase($containsIgnoreCase)
    {
        return $this->setOption('containsignorecase', $containsIgnoreCase);
    }

    /**
     * Get the case sensitivity of facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return bool
     */
    public function getContainsIgnoreCase()
    {
        return $this->getOption('containsignorecase');
    }

    /**
     * Set the facet sort order.
     *
     * Use one of the SORT_* constants as the value
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort($sort)
    {
        return $this->setOption('sort', $sort);
    }

    /**
     * Get the facet sort order.
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
     * Set the facet limit.
     *
     *  This is a global value for all facets in this facetset
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit($limit)
    {
        return $this->setOption('limit', $limit);
    }

    /**
     * Get the facet limit.
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
     * Set the facet mincount.
     *
     * This is a global value for all facets in this facetset
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($minCount)
    {
        return $this->setOption('mincount', $minCount);
    }

    /**
     * Get the facet mincount.
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
     * Set the missing count option.
     *
     * This is a global value for all facets in this facetset
     *
     * @param bool $missing
     *
     * @return self Provides fluent interface
     */
    public function setMissing($missing)
    {
        return $this->setOption('missing', $missing);
    }

    /**
     * Get the facet missing option.
     *
     * This is a global value for all facets in this facetset
     *
     * @return bool
     */
    public function getMissing()
    {
        return $this->getOption('missing');
    }

    /**
     * Add a facet.
     *
     *
     * @param \Solarium\Component\Facet\AbstractFacet|array $facet
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
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
        if (array_key_exists($key, $this->facets) && $this->facets[$key] !== $facet) {
            throw new InvalidArgumentException('A facet must have a unique key value within a query');
        }

        $this->facets[$key] = $facet;

        return $this;
    }

    /**
     * Add multiple facets.
     *
     * @param array $facets
     *
     * @return self Provides fluent interface
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
     * Get a facet.
     *
     * @param string $key
     *
     * @return string
     */
    public function getFacet($key)
    {
        if (isset($this->facets[$key])) {
            return $this->facets[$key];
        }
    }

    /**
     * Get all facets.
     *
     * @return AbstractFacet[]
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|\Solarium\Component\Facet\AbstractFacet $facet
     *
     * @return self Provides fluent interface
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
     * Remove all facets.
     *
     * @return self Provides fluent interface
     */
    public function clearFacets()
    {
        $this->facets = [];

        return $this;
    }

    /**
     * Set multiple facets.
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
     * Create a facet instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the facet
     * and it will be added to this query.
     * If you supply an options array/object that contains a key the facet will also be added to the query.
     *
     * When no key is supplied the facet cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addFacet method.
     *
     *
     * @param string            $type
     * @param array|object|null $options
     * @param bool              $add
     *
     * @throws OutOfBoundsException
     *
     * @return \Solarium\Component\Facet\AbstractFacet
     */
    public function createFacet($type, $options = null, $add = true)
    {
        $type = strtolower($type);

        if (!isset($this->facetTypes[$type])) {
            throw new OutOfBoundsException('Facettype unknown: '.$type);
        }

        $class = $this->facetTypes[$type];

        if (is_string($options)) {
            /** @var \Solarium\Component\Facet\Facet $facet */
            $facet = new $class();
            $facet->setKey($options);
        } else {
            $facet = new $class($options);
        }

        if ($add && null !== $facet->getKey()) {
            $this->addFacet($facet);
        }

        return $facet;
    }

    /**
     * Get a facet field instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Field
     */
    public function createFacetField($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_FIELD, $options, $add);
    }

    /**
     * Get a facet query instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Query
     */
    public function createFacetQuery($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_QUERY, $options, $add);
    }

    /**
     * Get a facet multiquery instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\MultiQuery
     */
    public function createFacetMultiQuery($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_MULTIQUERY, $options, $add);
    }

    /**
     * Get a facet range instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Range
     */
    public function createFacetRange($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_RANGE, $options, $add);
    }

    /**
     * Get a facet pivot instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Pivot
     */
    public function createFacetPivot($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_PIVOT, $options, $add);
    }

    /**
     * Get a facet interval instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Interval
     */
    public function createFacetInterval($options = null, $add = true)
    {
        return $this->createFacet(self::FACET_INTERVAL, $options, $add);
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
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
}
