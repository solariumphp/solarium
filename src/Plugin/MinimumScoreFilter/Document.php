<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Result\Document as SelectDocument;

/**
 * Minimum score filter query result document.
 *
 * Decorates the original document with a filter indicator
 */
class Document implements DocumentInterface, \IteratorAggregate, \Countable, \ArrayAccess, \JsonSerializable
{
    /**
     * Original document.
     *
     * @var SelectDocument
     */
    protected $document;

    /**
     * Is this document marked as a low score?
     *
     * @var bool
     */
    protected $marked;

    /**
     * Constructor.
     *
     * @param SelectDocument $document
     * @param float          $threshold
     */
    public function __construct(SelectDocument $document, float $threshold)
    {
        $this->document = $document;
        $this->marked = ($threshold > ($document->score ?? 0.0));
    }

    /**
     * Forward all other calls to the original document.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->document->$name($arguments);
    }

    /**
     * Forward get call to the original document.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->document->__get($name);
    }

    /**
     * Forward isset call to the original document.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->document->__isset($name);
    }

    /**
     * Set field value.
     *
     * Magic method for setting a field as property of this object. Since this
     * is a readonly document an exception will be thrown to prevent this.
     *
     * @param string $name
     * @param string $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value): void
    {
        throw new RuntimeException('A readonly document cannot be altered');
    }

    /**
     * Get markedAsLowScore status.
     *
     * @return bool
     */
    public function markedAsLowScore(): bool
    {
        return $this->marked;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->document->getIterator();
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->document->count();
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
        return $this->document->offsetExists($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->document->offsetUnset($offset);
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
        return $this->document->offsetGet($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->document->getFields();
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->document;
    }
}
