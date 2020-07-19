<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
     * @param int|null $matches
     * @param int|null $numberOfGroups
     * @param array    $groups
     */
    public function __construct(?int $matches, ?int $numberOfGroups, array $groups)
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
    public function getMatches(): int
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
    public function getNumberOfGroups(): int
    {
        return $this->numberOfGroups;
    }

    /**
     * Get all value groups.
     *
     * @return array
     */
    public function getValueGroups(): array
    {
        return $this->valueGroups;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->valueGroups);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->valueGroups);
    }
}
