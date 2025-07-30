<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\TermVector;

/**
 * Select component term vector result.
 */
class Result implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var Warnings
     */
    protected $warnings;

    /**
     * @var Document[]
     */
    protected $documents;

    /**
     * Constructor.
     *
     * @param Document[]    $documents
     * @param Warnings|null $warnings
     */
    public function __construct(array $documents, ?Warnings $warnings)
    {
        $this->documents = $documents;
        $this->warnings = $warnings;
    }

    /**
     * Get a document by its key.
     *
     * @param string $key
     *
     * @return Document|null
     */
    public function getDocument(string $key): ?Document
    {
        return $this->documents[$key] ?? null;
    }

    /**
     * Get all documents.
     *
     * @return Document[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * Get the warnings.
     *
     * @return Warnings|null
     */
    public function getWarnings(): ?Warnings
    {
        return $this->warnings;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->documents);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->documents);
    }

    #[\ReturnTypeWillChange]
    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->documents[$offset];
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        // Details are immutable.
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        // Details are immutable.
    }
}
