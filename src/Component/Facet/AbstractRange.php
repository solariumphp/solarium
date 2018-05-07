<?php

namespace Solarium\Component\Facet;

/**
 * Facet range.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Facet_by_Range
 */
abstract class AbstractRange extends AbstractFacet
{
    /**
     * Value for the 'other' option.
     */
    const OTHER_BEFORE = 'before';

    /**
     * Value for the 'other' option.
     */
    const OTHER_AFTER = 'after';

    /**
     * Value for the 'other' option.
     */
    const OTHER_BETWEEN = 'between';

    /**
     * Value for the 'other' option.
     */
    const OTHER_ALL = 'all';

    /**
     * Value for the 'other' option.
     */
    const OTHER_NONE = 'none';

    /**
     * Value for the 'include' option.
     */
    const INCLUDE_LOWER = 'lower';

    /**
     * Value for the 'include' option.
     */
    const INCLUDE_UPPER = 'upper';

    /**
     * Value for the 'include' option.
     */
    const INCLUDE_EDGE = 'edge';

    /**
     * Value for the 'include' option.
     */
    const INCLUDE_OUTER = 'outer';

    /**
     * Value for the 'include' option.
     */
    const INCLUDE_ALL = 'all';

    /**
     * Set the field name.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setField($field)
    {
        return $this->setOption('field', $field);
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getField()
    {
        return $this->getOption('field');
    }

    /**
     * Set the lower bound of the range.
     *
     * @param string $start
     *
     * @return self Provides fluent interface
     */
    public function setStart($start)
    {
        return $this->setOption('start', $start);
    }

    /**
     * Get the lower bound of the range.
     *
     * @return string
     */
    public function getStart()
    {
        return $this->getOption('start');
    }

    /**
     * Set the upper bound of the range.
     *
     * @param string $end
     *
     * @return self Provides fluent interface
     */
    public function setEnd($end)
    {
        return $this->setOption('end', $end);
    }

    /**
     * Get the upper bound of the range.
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->getOption('end');
    }

    /**
     * Set range gap.
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @param string $gap
     *
     * @return self Provides fluent interface
     */
    public function setGap($gap)
    {
        return $this->setOption('gap', $gap);
    }

    /**
     * Get range gap.
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @return string
     */
    public function getGap()
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
    public function setHardend($hardend)
    {
        return $this->setOption('hardend', $hardend);
    }

    /**
     * Get hardend option.
     *
     * @return bool
     */
    public function getHardend()
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
    public function setOther($other)
    {
        if (is_string($other)) {
            $other = explode(',', $other);
            $other = array_map('trim', $other);
        }

        return $this->setOption('other', $other);
    }

    /**
     * Get other counts.
     *
     * @return array
     */
    public function getOther()
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
    public function setInclude($include)
    {
        if (is_string($include)) {
            $include = explode(',', $include);
            $include = array_map('trim', $include);
        }

        return $this->setOption('include', $include);
    }

    /**
     * Get include option.
     *
     * @return array
     */
    public function getInclude()
    {
        $include = $this->getOption('include');
        if (null === $include) {
            $include = [];
        }

        return $include;
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
