<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Grouping;

use Solarium\Component\Result\ComponentResultInterface;

/**
 * Select component grouping result.
 *
 * @since 2.1.0
 */
class Result implements ComponentResultInterface, \IteratorAggregate, \Countable
{
    /**
     * Group results array.
     */
    protected array $groups;

    /**
     * Constructor.
     *
     * @param array $groups
     */
    public function __construct(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * Get all groups.
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Get a group.
     *
     * @param string $key
     *
     * @return FieldGroup|QueryGroup|null
     */
    public function getGroup(string $key): FieldGroup|QueryGroup|null
    {
        if (isset($this->groups[$key])) {
            return $this->groups[$key];
        }

        return null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->groups);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->groups);
    }
}
