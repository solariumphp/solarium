<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet;

/**
 * Select field facet result.
 *
 * A field facet will usually return a dataset of multiple rows, in each row a
 * value and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 */
class Field implements FacetResultInterface, \IteratorAggregate, \Countable
{
    /**
     * Value array.
     *
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Get all values.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->values);
    }
}
