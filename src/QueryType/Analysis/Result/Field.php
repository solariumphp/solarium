<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Analysis field query result.
 */
class Field extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * List instances array.
     *
     * @var array
     */
    protected $items;

    /**
     * Get all lists.
     *
     * @return array
     */
    public function getLists(): array
    {
        $this->parseResponse();

        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        $this->parseResponse();

        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return \count($this->items);
    }
}
