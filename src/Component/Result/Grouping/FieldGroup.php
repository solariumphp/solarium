<?php

namespace Solarium\Component\Result\Grouping;

/**
 * Select component grouping field group result.
 *
 * @since 2.1.0
 */
class FieldGroup implements \IteratorAggregate, \Countable
{
    /**
     * Match count.
     *
     * @var int
     */
    protected $matches;

    /**
     * Number of groups.
     *
     * @var int
     */
    protected $numberOfGroups;

    /**
     * Value groups.
     *
     * @var array
     */
    protected $valueGroups;

    /**
     * Constructor.
     *
     * @param int   $matches
     * @param int   $numberOfGroups
     * @param array $groups
     */
    public function __construct($matches, $numberOfGroups, $groups)
    {
        $this->matches = $matches;
        $this->numberOfGroups = $numberOfGroups;
        $this->valueGroups = $groups;
    }

    /**
     * Get matches value.
     *
     * @return int
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Get numberOfGroups value.
     *
     * Only available if the numberofgroups option in the query was 'true'
     *
     * @return int
     */
    public function getNumberOfGroups()
    {
        return $this->numberOfGroups;
    }

    /**
     * Get all value groups.
     *
     * @return array
     */
    public function getValueGroups()
    {
        return $this->valueGroups;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->valueGroups);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->valueGroups);
    }
}
