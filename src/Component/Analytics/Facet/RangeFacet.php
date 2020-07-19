<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet;

/**
 * Range Facet.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RangeFacet extends AbstractFacet
{
    /**
     * All gap-based ranges include their lower bound.
     */
    public const INCLUDE_LOWER = 'lower';

    /**
     * All gap-based ranges include their upper bound.
     */
    public const INCLUDE_UPPER = 'upper';

    /**
     * The first and last gap ranges include their edge bounds (lower for the first one,
     * upper for the last one) even if the corresponding upper/lower option is not specified.
     */
    public const INCLUDE_EDGE = 'edge';

    /**
     * The before and after ranges will be inclusive of their bounds,
     * even if the first or last ranges already include those boundaries.
     */
    public const INCLUDE_OUTER = 'outer';

    /**
     * Includes all options: lower, upper, edge, and outer.
     */
    public const INCLUDE_ALL = 'all';

    /**
     * All records with field values lower then lower bound of the first range.
     */
    public const OTHER_BEFORE = 'before';

    /**
     * All records with field values greater then the upper bound of the last range.
     */
    public const OTHER_AFTER = 'after';

    /**
     * All records with field values between the lower bound
     * of the first range and the upper bound of the last range.
     */
    public const OTHER_BETWEEN = 'between';

    /**
     * Include facet buckets for none of the above.
     */
    public const OTHER_NONE = 'none';

    /**
     * Include facet buckets for before, after and between.
     */
    public const OTHER_ALL = 'all';

    /**
     * Array of possible includes.
     */
    private const INCLUDES = [
        self::INCLUDE_LOWER,
        self::INCLUDE_UPPER,
        self::INCLUDE_EDGE,
        self::INCLUDE_OUTER,
        self::INCLUDE_ALL,
    ];

    /**
     * Array of possible others.
     */
    private const OTHERS = [
        self::OTHER_BEFORE,
        self::OTHER_AFTER,
        self::OTHER_BETWEEN,
        self::OTHER_NONE,
        self::OTHER_ALL,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @var array
     */
    private $gap = [];

    /**
     * @var bool
     */
    private $hardend = false;

    /**
     * @var array
     */
    private $include = [];

    /**
     * @var array
     */
    private $others = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractFacet::TYPE_RANGE;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @param int $start
     *
     * @return $this
     */
    public function setStart(int $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @param int $end
     *
     * @return $this
     */
    public function setEnd(int $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return array
     */
    public function getGap(): array
    {
        return $this->gap;
    }

    /**
     * @param array $gap
     *
     * @return $this
     */
    public function setGap(array $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHardend(): bool
    {
        return $this->hardend;
    }

    /**
     * @param bool $hardend
     *
     * @return $this
     */
    public function setHardend(bool $hardend): self
    {
        $this->hardend = $hardend;

        return $this;
    }

    /**
     * @return array
     */
    public function getInclude(): array
    {
        return array_values(array_intersect(self::INCLUDES, $this->include));
    }

    /**
     * @param array $include
     *
     * @return $this
     */
    public function setInclude(array $include): self
    {
        $this->include = $include;

        return $this;
    }

    /**
     * @return array
     */
    public function getOthers(): array
    {
        return array_values(array_intersect(self::OTHERS, $this->others));
    }

    /**
     * @param array $others
     *
     * @return $this
     */
    public function setOthers(array $others): self
    {
        $this->others = $others;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter(
            [
                'type' => $this->getType(),
                'field' => $this->field,
                'start' => $this->start,
                'end' => $this->end,
                'gap' => $this->gap,
                'hardend' => $this->hardend,
                'include' => $this->include,
                'others' => $this->others,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );
    }
}
