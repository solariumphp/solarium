<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\TermVector;

/**
 * Select component term vector document result.
 */
class Document implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var string|null
     */
    protected $uniqueKey;

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param string|null $uniqueKey
     * @param Field[]     $fields
     */
    public function __construct(?string $uniqueKey, array $fields)
    {
        $this->uniqueKey = $uniqueKey;
        $this->fields = $fields;
    }

    /**
     * Returns the document's unique key.
     *
     * Will always return null if the schema has no unique key defined.
     *
     * @return string|null
     */
    public function getUniqueKey(): ?string
    {
        return $this->uniqueKey;
    }

    /**
     * Returns the fields with term vectors.
     *
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Returns a field with term vectors by name.
     *
     * @return Field|null
     */
    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->fields);
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
        return \array_key_exists($offset, $this->fields);
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
        return $this->fields[$offset];
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
