<?php

namespace Solarium\QueryType\ManagedResources\Result\Resources;

class ResourcesList implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name = 'managedResources';

    /**
     * List items.
     *
     * @var Resoure[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get all items.
     *
     * @return Resource[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->items);
    }
}
