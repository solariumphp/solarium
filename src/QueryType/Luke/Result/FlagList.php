<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result;

/**
 * Immutable {@see Flag} list.
 */
class FlagList implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var string
     */
    protected $flags;

    /**
     * @var Flag[]
     */
    protected $list = [];

    /**
     * @var string[]
     */
    protected $lookup = [];

    /**
     * Iterator position.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Iterator length.
     *
     * @var int
     */
    protected $length;

    /**
     * Constructor.
     *
     * @param string $flags The flags string that was returned by Solr
     * @param array  $key   The info key that was returned by Solr
     */
    public function __construct(string $flags, array $key)
    {
        $this->flags = $flags;
        $index = 0;

        foreach (str_split($flags) as $flag) {
            if ('-' !== $flag) {
                $this->list[$index] = new Flag($flag, $key[$flag]);
                $this->lookup[$index] = $flag;
                ++$index;
            }
        }

        $this->length = $index;
    }

    /**
     * @return bool
     */
    public function isIndexed(): ?bool
    {
        return \in_array('I', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isTokenized(): bool
    {
        return \in_array('T', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isStored(): bool
    {
        return \in_array('S', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isDocValues(): bool
    {
        return \in_array('D', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isUninvertible(): bool
    {
        return \in_array('U', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isMultiValued(): bool
    {
        return \in_array('M', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isTermVectors(): bool
    {
        return \in_array('V', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isTermOffsets(): bool
    {
        return \in_array('o', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isTermPositions(): bool
    {
        return \in_array('p', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isTermPayloads(): bool
    {
        return \in_array('y', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isOmitNorms(): bool
    {
        return \in_array('O', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isOmitTermFreqAndPositions(): bool
    {
        return \in_array('F', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isOmitPositions(): bool
    {
        return \in_array('P', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isStoreOffsetsWithPositions(): bool
    {
        return \in_array('H', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isLazy(): bool
    {
        return \in_array('L', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isBinary(): bool
    {
        return \in_array('B', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isSortMissingFirst(): bool
    {
        return \in_array('f', $this->lookup);
    }

    /**
     * @return bool
     */
    public function isSortMissingLast(): bool
    {
        return \in_array('l', $this->lookup);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->length;
    }

    /**
     * Iterator implementation.
     *
     * @return Flag
     */
    public function current(): Flag
    {
        return $this->list[$this->position];
    }

    /**
     * Iterator implementation.
     *
     * @return string
     */
    public function key(): string
    {
        return $this->lookup[$this->position];
    }

    /**
     * Iterator implementation.
     *
     * @return void
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Iterator implementation.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Iterator implementation.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->position < $this->length;
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
        return \in_array($offset, $this->lookup);
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
        $value = null;

        if (false !== $index = array_search($offset, $this->lookup)) {
            $value = $this->list[$index];
        }

        return $value;
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

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->flags;
    }
}
