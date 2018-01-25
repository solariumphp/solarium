<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSet;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
 */
class Field extends AbstractFacet
{
    /**
     * Facet sort type index.
     */
    const SORT_INDEX = 'index';

    /**
     * Facet sort type count.
     */
    const SORT_COUNT = 'count';

    /**
     * Facet method enum.
     */
    const METHOD_ENUM = 'enum';

    /**
     * Facet method fc.
     */
    const METHOD_FC = 'fc';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'field' => 'id',
    ];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_FIELD;
    }

    /**
     * Set the field name.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setField($field)
    {
        return $this->setOption('field', $field);
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getField()
    {
        return $this->getOption('field');
    }

    /**
     * Set the facet sort order.
     *
     * Use one of the SORT_* constants as the value
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
     * @return string
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }

    /**
     * Limit the terms for faceting by a prefix.
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
     * Set the facet limit.
     *
     * @param mixed $limit
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
     * @return string
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet offset.
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     */
    public function setOffset($offset)
    {
        return $this->setOption('offset', $offset);
    }

    /**
     * Get the facet offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->getOption('offset');
    }

    /**
     * Set the facet mincount.
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
     * @return int
     */
    public function getMinCount()
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the missing count option.
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
     * @return bool
     */
    public function getMissing()
    {
        return $this->getOption('missing');
    }

    /**
     * Set the facet method.
     *
     * Use one of the METHOD_* constants as value
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod($method)
    {
        return $this->setOption('method', $method);
    }

    /**
     * Get the facet method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getOption('method');
    }
}
