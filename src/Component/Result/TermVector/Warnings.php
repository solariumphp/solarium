<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\TermVector;

/**
 * Select component term vector warnings result.
 */
class Warnings implements \ArrayAccess
{
    /**
     * @var string[]|null
     */
    protected $noTermVectors;

    /**
     * @var string[]|null
     */
    protected $noPositions;

    /**
     * @var string[]|null
     */
    protected $noOffsets;

    /**
     * @var string[]|null
     */
    protected $noPayloads;

    /**
     * @param string[]|null $noTermVectors
     * @param string[]|null $noPositions
     * @param string[]|null $noOffsets
     * @param string[]|null $noPayloads
     */
    public function __construct(?array $noTermVectors, ?array $noPositions, ?array $noOffsets, ?array $noPayloads)
    {
        $this->noTermVectors = $noTermVectors;
        $this->noPositions = $noPositions;
        $this->noOffsets = $noOffsets;
        $this->noPayloads = $noPayloads;
    }

    /**
     * Returns the names of the requested fields that don't have term vectors.
     *
     * @return string[]
     */
    public function getNoTermVectors(): array
    {
        return $this->noTermVectors ?? [];
    }

    /**
     * Returns the names of the requested fields that don't have position information.
     *
     * @return string[]
     */
    public function getNoPositions(): array
    {
        return $this->noPositions ?? [];
    }

    /**
     * Returns the names of the requested fields that don't have offset information.
     *
     * @return string[]
     */
    public function getNoOffsets(): array
    {
        return $this->noOffsets ?? [];
    }

    /**
     * Returns the names of the requested fields that don't have payload information.
     *
     * @return string[]
     */
    public function getNoPayloads(): array
    {
        return $this->noPayloads ?? [];
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
        return \in_array($offset, ['noTermVectors', 'noPositions', 'noOffsets', 'noPayloads']);
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
        return $this->{$offset};
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
