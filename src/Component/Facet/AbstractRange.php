<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Core\Configurable;

/**
 * Facet range.
 *
 * @see https://solr.apache.org/guide/faceting.html#range-faceting
 */
abstract class AbstractRange extends AbstractFacet
{
    /**
     * Value for the 'other' option.
     */
    public const OTHER_BEFORE = 'before';

    /**
     * Value for the 'other' option.
     */
    public const OTHER_AFTER = 'after';

    /**
     * Value for the 'other' option.
     */
    public const OTHER_BETWEEN = 'between';

    /**
     * Value for the 'other' option.
     */
    public const OTHER_ALL = 'all';

    /**
     * Value for the 'other' option.
     */
    public const OTHER_NONE = 'none';

    /**
     * Value for the 'include' option.
     */
    public const INCLUDE_LOWER = 'lower';

    /**
     * Value for the 'include' option.
     */
    public const INCLUDE_UPPER = 'upper';

    /**
     * Value for the 'include' option.
     */
    public const INCLUDE_EDGE = 'edge';

    /**
     * Value for the 'include' option.
     */
    public const INCLUDE_OUTER = 'outer';

    /**
     * Value for the 'include' option.
     */
    public const INCLUDE_ALL = 'all';

    /**
     * Set the field name.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setField(string $field): self
    {
        $this->setOption('field', $field);

        return $this;
    }

    /**
     * Get the field name.
     *
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->getOption('field');
    }

    /**
     * Set the lower bound of the range.
     *
     * @param string|int|float $start A date expression or a number
     *
     * @return self Provides fluent interface
     */
    public function setStart($start): self
    {
        $this->setOption('start', (string) $start);

        return $this;
    }

    /**
     * Get the lower bound of the range.
     *
     * @return string|null
     */
    public function getStart(): ?string
    {
        return $this->getOption('start');
    }

    /**
     * Set the upper bound of the range.
     *
     * @param string|int|float $end A date expression or a number
     *
     * @return self Provides fluent interface
     */
    public function setEnd($end): self
    {
        $this->setOption('end', (string) $end);

        return $this;
    }

    /**
     * Get the upper bound of the range.
     *
     * @return string|null
     */
    public function getEnd(): ?string
    {
        return $this->getOption('end');
    }

    /**
     * Set range gap.
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @param string|int|float $gap A date expression or a number
     *
     * @return self Provides fluent interface
     */
    public function setGap($gap): self
    {
        $this->setOption('gap', $gap);

        return $this;
    }

    /**
     * Get range gap.
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @return string|null
     */
    public function getGap(): ?string
    {
        return $this->getOption('gap');
    }

    /**
     * Set hardend option.
     *
     * A Boolean parameter instructing Solr what to do in the event that facet.range.gap
     * does not divide evenly between facet.range.start and facet.range.end
     *
     * @param bool $hardend
     *
     * @return self Provides fluent interface
     */
    public function setHardend(bool $hardend): self
    {
        $this->setOption('hardend', $hardend);

        return $this;
    }

    /**
     * Get hardend option.
     *
     * @return bool|null
     */
    public function getHardend(): ?bool
    {
        return $this->getOption('hardend');
    }

    /**
     * Set other counts.
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|array $other
     *
     * @return self Provides fluent interface
     */
    public function setOther($other): self
    {
        if (\is_string($other)) {
            $other = explode(',', $other);
            $other = array_map('trim', $other);
        }

        $this->setOption('other', $other);

        return $this;
    }

    /**
     * Get other counts.
     *
     * @return array
     */
    public function getOther(): array
    {
        $other = $this->getOption('other');
        if (null === $other) {
            $other = [];
        }

        return $other;
    }

    /**
     * Set include option.
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|array $include
     *
     * @return self Provides fluent interface
     */
    public function setInclude($include): self
    {
        if (\is_string($include)) {
            $include = explode(',', $include);
            $include = array_map('trim', $include);
        }

        $this->setOption('include', $include);

        return $this;
    }

    /**
     * Get include option.
     *
     * @return array
     */
    public function getInclude(): array
    {
        $include = $this->getOption('include');
        if (null === $include) {
            $include = [];
        }

        return $include;
    }

    /**
     * @param \Solarium\Component\Facet\Pivot|array $pivot
     *
     * @return \Solarium\Core\Configurable
     */
    public function setPivot($pivot): Configurable
    {
        return $this->setOption('pivot', $pivot);
    }

    /**
     * @return \Solarium\Component\Facet\Pivot|array|null
     */
    public function getPivot()
    {
        return $this->getOption('pivot');
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'include':
                    $this->setInclude($value);
                    break;
                case 'other':
                    $this->setOther($value);
                    break;
            }
        }
    }
}
