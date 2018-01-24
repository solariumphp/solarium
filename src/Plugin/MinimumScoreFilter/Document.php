<?php

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Result\DocumentInterface;

/**
 * Minimum score filter query result document.
 *
 * Decorates the original document with a filter indicator
 */
class Document implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * Original document.
     *
     * @var array
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
     * @param DocumentInterface $document
     * @param int               $threshold
     */
    public function __construct(DocumentInterface $document, $threshold)
    {
        $this->document = $document;
        $this->marked = $threshold > $document->score;
    }

    /**
     * Forward all other calls to the original document.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->document->$name($arguments);
    }

    /**
     * Forward all other calls to the original document.
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
    public function __isset($name)
    {
        return $this->document->__isset($name);
    }

    /**
     * Set field value.
     *
     * Magic method for setting a field as property of this object. Since this
     * is a readonly document an exception will be thrown to prevent this.
     *
     *
     * @param string $name
     * @param string $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        throw new RuntimeException('A readonly document cannot be altered');
    }

    /**
     * Get markedAsLowScore status.
     *
     * @return bool
     */
    public function markedAsLowScore()
    {
        return $this->marked;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->document->getIterator();
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
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
    public function offsetExists($offset)
    {
        return $this->document->offsetExists($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->document->offsetUnset($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return mixed|null
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
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }
}
