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
 * Analysis document query result.
 */
class Document extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Document instances array.
     *
     * @var ResultList[]
     */
    protected $items;

    /**
     * Get all documents.
     *
     * @return ResultList[]
     */
    public function getDocuments(): array
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

    /**
     * Get a document by uniquekey value.
     *
     * @param string $key
     *
     * @return ResultList|null
     */
    public function getDocument(string $key): ?ResultList
    {
        $this->parseResponse();

        return $this->items[$key] ?? null;
    }
}
