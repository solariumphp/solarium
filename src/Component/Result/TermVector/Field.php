<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\TermVector;

/**
 * Select component term vector document field result.
 */
class Field implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Term[]
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param string $name
     * @param Term[] $terms
     */
    public function __construct(string $name, array $terms)
    {
        $this->name = $name;
        $this->terms = $terms;
    }

    /**
     * Returns the field name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the info for all terms.
     *
     * @return Term[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

    /**
     * Returns the info for a term.
     *
     * @param string $term
     *
     * @return Term|null
     */
    public function getTerm(string $term): ?Term
    {
        return $this->terms[$term] ?? null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->terms);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->terms);
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
        return \array_key_exists($offset, $this->terms);
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
        return $this->terms[$offset];
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
