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
    public const SORT_INDEX = 'index';

    /**
     * Facet sort type count.
     */
    public const SORT_COUNT = 'count';

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
    public function setField(string $field): self
    {
        $this->setOption('field', $field);
        return $this;
    }

    /**
     * Get the field name.
     *
     * @return string|null
     */
    public function getField(): ?string
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
    public function setSort(string $sort): self
    {
        $this->setOption('sort', $sort);
        return $this;
    }

    /**
     * Get the facet sort order.
     *
     * @return string|null
     */
    public function getSort(): ?string
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
    public function setPrefix(string $prefix): self
    {
        $this->setOption('prefix', $prefix);
        return $this;
    }

    /**
     * Get the facet prefix.
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->getOption('prefix');
    }

    /**
     * Set the facet limit.
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit(int $limit): self
    {
        $this->setOption('limit', $limit);
        return $this;
    }

    /**
     * Get the facet limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
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
    public function setOffset(int $offset): self
    {
        $this->setOption('offset', $offset);
        return $this;
    }

    /**
     * Get the facet offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
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
    public function setMinCount(int $minCount): self
    {
        $this->setOption('mincount', $minCount);
        return $this;
    }

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
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
    public function setMissing(bool $missing): self
    {
        $this->setOption('missing', $missing);
        return $this;
    }

    /**
     * Get the facet missing option.
     *
     * @return bool|null
     */
    public function getMissing(): ?bool
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
    public function setMethod(string $method): self
    {
        $this->setOption('method', $method);
        return $this;
    }

    /**
     * Get the facet method.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }
}
