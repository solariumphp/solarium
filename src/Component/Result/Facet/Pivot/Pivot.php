<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet\Pivot;

use Solarium\Component\Result\Facet\FacetResultInterface;

/**
 * Select field pivot result.
 */
class Pivot implements FacetResultInterface, \IteratorAggregate, \Countable
{
    /**
     * Value array.
     *
     * @var array
     */
    protected $pivot = [];

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $pivotData) {
            $this->pivot[] = new PivotItem($pivotData);
        }
    }

    /**
     * Get pivot results.
     *
     * @return Pivot[]
     */
    public function getPivot(): array
    {
        return $this->pivot;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->pivot);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->pivot);
    }
}
