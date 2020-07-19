<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet\Pivot;

use Solarium\Component\Result\Facet\Range;
use Solarium\Component\Result\Stats\Stats;

/**
 * Select field pivot result.
 */
class PivotItem extends Pivot
{
    /**
     * Field name.
     *
     * @var string
     */
    protected $field;

    /**
     * Field value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Count.
     *
     * @var int
     */
    protected $count;

    /**
     * Field stats.
     *
     * @var Stats|null
     */
    protected $stats;

    /**
     * @var \Solarium\Component\Result\Facet\Range[]
     */
    protected $ranges;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct([]);

        $this->field = $data['field'];
        $this->value = $data['value'];
        $this->count = $data['count'];

        if (isset($data['pivot'])) {
            foreach ($data['pivot'] as $pivotData) {
                $this->pivot[] = new self($pivotData);
            }
        }

        if (isset($data['stats'])) {
            $this->stats = new Stats($data['stats']);
        }

        if (isset($data['ranges'])) {
            foreach ($data['ranges'] as $range) {
                $before = $range['before'] ?? null;
                $after = $range['after'] ?? null;
                $between = $range['between'] ?? null;
                $start = $range['start'] ?? null;
                $end = $range['end'] ?? null;
                $gap = $range['gap'] ?? null;
                $this->ranges[] = new Range($range['counts'], $before, $after, $between, $start, $end, $gap);
            }
        }
    }

    /**
     * Get field name.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get field value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get stats.
     *
     * @return Stats|null
     */
    public function getStats(): ?Stats
    {
        return $this->stats;
    }

    /**
     * Get ranges.
     *
     * @return \Solarium\Component\Result\Facet\Range[]
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }
}
