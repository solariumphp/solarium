<?php

namespace Solarium\Component\Result\Facet;

/**
 * Select query facet result.
 *
 * Since a query facet has only a single result, the count for the query, this
 * is a very simple object.
 */
class Query implements FacetResultInterface
{
    /**
     * Value (count).
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
