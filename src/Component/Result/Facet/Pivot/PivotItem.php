<?php

namespace Solarium\Component\Result\Facet\Pivot;

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
     * @var mixed
     */
    protected $stats;

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
     * @return Stats
     */
    public function getStats(): Stats
    {
        return $this->stats;
    }
}
