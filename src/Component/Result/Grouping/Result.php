<?php

namespace Solarium\Component\Result\Grouping;

/**
 * Select component grouping result.
 *
 * @since 2.1.0
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Group results array.
     *
     * @var array
     */
    protected $groups;

    /**
     * Constructor.
     *
     * @param array $groups
     */
    public function __construct($groups)
    {
        $this->groups = $groups;
    }

    /**
     * Get all groups.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get a group.
     *
     * @param string $key
     *
     * @return FieldGroup|QueryGroup
     */
    public function getGroup($key)
    {
        if (isset($this->groups[$key])) {
            return $this->groups[$key];
        }
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->groups);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->groups);
    }
}
