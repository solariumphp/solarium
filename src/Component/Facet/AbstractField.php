<?php

namespace Solarium\Component\Facet;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
 */
abstract class AbstractField extends AbstractFacet
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
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'field' => 'id',
    ];

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
