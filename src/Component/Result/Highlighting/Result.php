<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Highlighting;

/**
 * Select component highlighting result item.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Fields array.
     *
     * @var array
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Get highlights for all fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get highlights for a single field.
     *
     * @param string $key
     *
     * @return array
     */
    public function getField($key): array
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }

        return [];
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
}
