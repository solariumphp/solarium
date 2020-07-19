<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug documentset result.
 */
class DocumentSet implements \IteratorAggregate, \Countable
{
    /**
     * Docs array.
     *
     * @var array
     */
    protected $docs;

    /**
     * Constructor.
     *
     * @param array $docs
     */
    public function __construct(array $docs)
    {
        $this->docs = $docs;
    }

    /**
     * Get a document by key.
     *
     * @param mixed $key
     *
     * @return Document|null
     */
    public function getDocument($key): ?Document
    {
        return $this->docs[$key] ?? null;
    }

    /**
     * Get all docs.
     *
     * @return array
     */
    public function getDocuments(): array
    {
        return $this->docs;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->docs);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->docs);
    }
}
