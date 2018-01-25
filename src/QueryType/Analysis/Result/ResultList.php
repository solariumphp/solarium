<?php

namespace Solarium\QueryType\Analysis\Result;

/**
 * Analysis list result.
 */
class ResultList implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name;

    /**
     * List items.
     *
     * @var array
     */
    protected $items;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $items
     */
    public function __construct($name, $items)
    {
        $this->name = $name;
        $this->items = $items;
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get all items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
}
