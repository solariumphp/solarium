<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    public function __construct(string $name, array $items)
    {
        $this->name = $name;
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
     * @return array
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
