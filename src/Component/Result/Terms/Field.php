<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Terms;

/**
 * Terms component result.
 */
class Field implements \IteratorAggregate, \Countable
{
    /**
     * Terms.
     *
     * @var array
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param array $terms
     */
    public function __construct(array $terms)
    {
        $this->terms = $terms;
    }

    /**
     * Get Terms.
     *
     * @return array
     */
    public function getTerms(): array
    {
        return array_keys($this->terms);
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
}
